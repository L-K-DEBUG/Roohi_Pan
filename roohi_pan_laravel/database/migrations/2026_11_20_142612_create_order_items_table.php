<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->onDelete('cascade');
                $table->foreignId('item_id')->constrained();
                $table->integer('quantity');
                $table->decimal('price', 10, 2);
                $table->timestamps();
            });

            return;
        }

        $foreignColumns = collect(Schema::getForeignKeys('order_items'))
            ->flatMap(fn (object $foreignKey) => $foreignKey->columns)
            ->all();

        Schema::table('order_items', function (Blueprint $table) use ($foreignColumns) {
            if (! in_array('order_id', $foreignColumns, true)) {
                $table->foreign('order_id')
                    ->references('id')
                    ->on('orders')
                    ->onDelete('cascade');
            }

            if (! in_array('item_id', $foreignColumns, true)) {
                $table->foreign('item_id')->references('id')->on('items');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
