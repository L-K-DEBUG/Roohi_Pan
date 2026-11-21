<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Item;
use App\Models\Order;
use App\Support\InventoryRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(): View
    {
        $items = Item::query()
            ->where('stock', '>', 0)
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return view('pos.index', compact('items'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'order_type' => ['required', 'in:Dine-in,Delivery'],
            'table_no' => ['nullable', 'string', 'max:20'],
            'customer_name' => ['required', 'string', 'max:100'],
            'payment_status' => ['required', 'in:paid,unpaid'],
            'payment_method' => ['required_if:payment_status,paid', 'nullable', 'in:cash,mpesa'],
            'cart' => ['required', 'array', 'min:1'],
            'cart.*.id' => ['required', 'integer', 'distinct'],
            'cart.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $order = DB::transaction(function () use ($data): Order {
            $cart = collect($data['cart'])->keyBy(fn (array $line) => (int) $line['id']);
            $items = Item::query()
                ->whereIn('id', $cart->keys())
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($items->count() !== $cart->count()) {
                throw ValidationException::withMessages([
                    'cart' => 'One or more selected menu items are no longer available.',
                ]);
            }

            foreach ($cart as $id => $line) {
                if ($items[$id]->stock < $line['quantity']) {
                    throw ValidationException::withMessages([
                        'cart' => "{$items[$id]->name} does not have enough stock.",
                    ]);
                }
            }

            $requiredInventory = [];
            foreach ($cart as $id => $line) {
                foreach (InventoryRules::deductionsFor($items[$id]) as $deduction) {
                    $inventoryName = $deduction['item_name'];
                    $requiredInventory[$inventoryName] = ($requiredInventory[$inventoryName] ?? 0)
                        + $deduction['quantity'] * (int) $line['quantity'];
                }
            }

            ksort($requiredInventory);
            foreach ($requiredInventory as $inventoryName => $requiredQuantity) {
                $inventory = Inventory::firstOrCreate(
                    ['item_name' => $inventoryName],
                    ['quantity' => 0],
                );
                $inventory = Inventory::query()->lockForUpdate()->findOrFail($inventory->id);

                if ($inventory->quantity < $requiredQuantity) {
                    throw ValidationException::withMessages([
                        'cart' => "There is not enough {$inventoryName} in stock to place this order.",
                    ]);
                }
            }

            $total = 0;
            foreach ($cart as $id => $line) {
                $total += $items[$id]->price * $line['quantity'];
            }

            $order = Order::create([
                'user_id' => auth()->id(),
                'order_type' => $data['order_type'],
                'table_no' => $data['table_no'] ?? null,
                'customer_name' => $data['customer_name'],
                'total_amount' => $total,
                'payment_status' => $data['payment_status'],
                'payment_method' => $data['payment_status'] === 'paid' ? $data['payment_method'] : null,
                'status' => 'completed',
            ]);

            foreach ($cart as $id => $line) {
                $item = $items[$id];
                $quantity = (int) $line['quantity'];
                $order->items()->create([
                    'item_id' => $item->id,
                    'quantity' => $quantity,
                    'price' => $item->price,
                ]);
                $item->decrement('stock', $quantity);

                $this->updateInventory($item, $quantity);
            }

            return $order;
        });

        return redirect()->route('orders.receipt', $order);
    }

    public function receipt(Order $order): View
    {
        $order->load(['user', 'items.item']);

        return view('orders.receipt', compact('order'));
    }

    public function cancel(Order $order): RedirectResponse
    {
        DB::transaction(function () use ($order): void {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($lockedOrder->status !== 'completed') {
                return;
            }

            $lockedOrder->load('items.item');
            foreach ($lockedOrder->items as $line) {
                Item::query()->whereKey($line->item_id)->increment('stock', $line->quantity);
                $this->updateInventory($line->item, $line->quantity, true);
            }

            $lockedOrder->update(['status' => 'cancelled']);
        });

        return redirect()->route('pos.index')->with('status', 'Order cancelled successfully.');
    }

    private function updateInventory(Item $item, int $quantity, bool $restore = false): void
    {
        foreach (InventoryRules::deductionsFor($item) as $deduction) {
            $amount = $deduction['quantity'] * $quantity;
            $inventory = Inventory::firstOrCreate(
                ['item_name' => $deduction['item_name']],
                ['quantity' => 0],
            );
            $inventory = Inventory::query()->lockForUpdate()->findOrFail($inventory->id);
            $method = $restore ? 'increment' : 'decrement';
            $inventory->{$method}('quantity', $amount);
        }
    }
}
