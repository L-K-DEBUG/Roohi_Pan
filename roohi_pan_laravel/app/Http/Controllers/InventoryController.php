<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Item;
use App\Support\InventoryRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        $this->ensureOwner();

        $groups = [];
        foreach (InventoryRules::ingredientGroups() as $group => $names) {
            $groups[$group] = $this->inventoryItems($names);
        }

        $drinks = Item::query()
            ->whereIn('category', ['SOFT DRINKS (CAN)', 'SOFT DRINKS (BOTTLE)'])
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        $groups['Canned Soft Drinks'] = $this->inventoryItems(
            $drinks->get('SOFT DRINKS (CAN)', collect())->pluck('name')->all(),
        );
        $groups['Bottled Soft Drinks'] = $this->inventoryItems(
            $drinks->get('SOFT DRINKS (BOTTLE)', collect())->pluck('name')->all(),
        );

        return view('inventory.index', compact('groups'));
    }

    public function adjust(Request $request, Inventory $inventory): RedirectResponse
    {
        $this->ensureOwner();
        abort_unless(in_array($inventory->item_name, $this->trackedNames(), true), 404);

        $data = $request->validate([
            'action' => ['required', Rule::in(['add', 'subtract'])],
            'quantity' => [
                'required',
                'numeric',
                'gt:0',
                ...($inventory->item_name === 'Chicken Breast' ? ['multiple_of:0.5'] : ['integer']),
            ],
        ]);

        $updated = DB::transaction(function () use ($inventory, $data): bool {
            $lockedInventory = Inventory::query()->lockForUpdate()->findOrFail($inventory->id);

            if ($data['action'] === 'subtract' && $lockedInventory->quantity < $data['quantity']) {
                return false;
            }

            $method = $data['action'] === 'add' ? 'increment' : 'decrement';
            $lockedInventory->{$method}('quantity', $data['quantity']);

            return true;
        });

        if (! $updated) {
            return redirect()
                ->to(route('inventory.index').'#inventory-item-'.$inventory->id)
                ->withErrors(['quantity' => 'Quantity cannot be reduced below zero.']);
        }

        return redirect()
            ->to(route('inventory.index').'#inventory-item-'.$inventory->id)
            ->with('status', 'Inventory quantity updated.');
    }

    private function ensureOwner(): void
    {
        abort_unless(auth()->user()->isOwner(), 403);
    }

    private function inventoryItems(array $names): array
    {
        return collect($names)
            ->map(fn (string $name) => Inventory::firstOrCreate(
                ['item_name' => $name],
                ['quantity' => 0],
            ))
            ->all();
    }

    private function trackedNames(): array
    {
        $ingredientNames = array_merge(...array_values(InventoryRules::ingredientGroups()));
        $drinkNames = Item::query()
            ->whereIn('category', ['SOFT DRINKS (CAN)', 'SOFT DRINKS (BOTTLE)'])
            ->pluck('name')
            ->all();

        return array_merge($ingredientNames, $drinkNames);
    }
}
