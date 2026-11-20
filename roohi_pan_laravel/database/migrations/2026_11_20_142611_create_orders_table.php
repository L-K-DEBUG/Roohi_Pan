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
        if (! Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained();
                $table->string('order_type');
                $table->string('table_no')->nullable();
                $table->string('customer_name');
                $table->decimal('total_amount', 10, 2);
                $table->enum('payment_status', ['paid', 'unpaid'])->default('paid');
                $table->enum('status', ['completed', 'cancelled'])->default('completed');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
