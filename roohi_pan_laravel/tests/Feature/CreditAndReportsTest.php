<?php

use App\Models\Item;
use App\Models\Order;
use App\Models\User;

test('credit can be settled and completed orders appear in reports', function () {
    $user = User::factory()->create(['role' => 'employee']);
    $item = Item::create([
        'name' => 'Vanilla Faluda',
        'category' => 'FALUDA',
        'price' => 400,
        'stock' => 10,
    ]);
    $order = Order::create([
        'user_id' => $user->id,
        'order_type' => 'Dine-in',
        'customer_name' => 'Customer',
        'total_amount' => 800,
        'payment_status' => 'unpaid',
        'status' => 'completed',
    ]);
    $order->items()->create([
        'item_id' => $item->id,
        'quantity' => 2,
        'price' => 400,
    ]);

    $this->actingAs($user)
        ->get(route('reports.index', ['filter' => 'today']))
        ->assertOk()
        ->assertSee('KSh 800')
        ->assertSee('Vanilla Faluda');

    $this->get(route('credit.index'))
        ->assertOk()
        ->assertSee('Customer')
        ->assertSee('KSh 800');

    $this->post(route('credit.settle', $order))->assertRedirect(route('credit.index'));
    $this->assertSame('paid', $order->fresh()->payment_status);
});
