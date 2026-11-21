<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('inventory')) {
            return;
        }

        $legacyNames = ['Possin Chicken Portions', 'Fried Chicken Portions'];

        DB::transaction(function () use ($legacyNames): void {
            $legacyRows = DB::table('inventory')->whereIn('item_name', $legacyNames);

            if (! $legacyRows->exists()) {
                return;
            }

            $quantity = (float) $legacyRows->sum('quantity');
            $chicken = DB::table('inventory')->where('item_name', 'Chicken')->first();
            $quantity += (float) ($chicken->quantity ?? 0);

            if ($chicken) {
                DB::table('inventory')
                    ->where('item_name', 'Chicken')
                    ->update(['quantity' => $quantity, 'updated_at' => now()]);
            } else {
                DB::table('inventory')->insert([
                    'item_name' => 'Chicken',
                    'quantity' => $quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('inventory')->whereIn('item_name', $legacyNames)->delete();
        });
    }

    public function down(): void
    {
        // The merged Chicken balance cannot be split back into the original quantities.
    }
};
