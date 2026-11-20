<?php

use App\Models\Inventory;
use App\Models\Item;
use App\Models\Order;
use App\Models\User;

test('employees can place, print, and cancel an order', function () {
    $user = User::factory()->create(['role' => 'employee']);
    $item = Item::create([
        'name' => 'Chapati',
        'category' => 'SNACKS',
        'price' => 30,
        'stock' => 5,
    ]);
    $inventory = Inventory::create(['item_name' => 'Chapati', 'quantity' => 10]);

    $this->actingAs($user)
        ->get(route('pos.index'))
        ->assertOk()
        ->assertSee('Chapati');

    $response = $this->post(route('orders.store'), [
        'order_type' => 'Dine-in',
        'customer_name' => 'Walk-up',
        'payment_status' => 'unpaid',
        'cart' => [
            ['id' => $item->id, 'quantity' => 2],
        ],
    ]);

    $response->assertSessionHasNoErrors();
    $order = Order::query()->firstOrFail();
    $response->assertRedirect(route('orders.receipt', $order));
    $this->assertSame('60.00', $order->total_amount);
    $this->assertSame(3, $item->fresh()->stock);
    $this->assertSame('8.00', $inventory->fresh()->quantity);
    $this->get(route('orders.receipt', $order))->assertOk()->assertSee('UNPAID - CREDIT');

    $this->post(route('orders.cancel', $order))->assertRedirect(route('pos.index'));
    $this->assertSame('cancelled', $order->fresh()->status);
    $this->assertSame(5, $item->fresh()->stock);
    $this->assertSame('10.00', $inventory->fresh()->quantity);
});

test('an order cannot exceed available menu stock', function () {
    $user = User::factory()->create();
    $item = Item::create([
        'name' => 'Limited item',
        'category' => 'SNACKS',
        'price' => 30,
        'stock' => 1,
    ]);

    $this->actingAs($user)
        ->post(route('orders.store'), [
            'order_type' => 'Dine-in',
            'customer_name' => 'Walk-up',
            'payment_status' => 'paid',
            'cart' => [['id' => $item->id, 'quantity' => 2]],
        ])
        ->assertSessionHasErrors('cart');

    $this->assertDatabaseCount('orders', 0);
    $this->assertSame(1, $item->fresh()->stock);
});

test('only owners can use staff and inventory management', function () {
    $employee = User::factory()->create(['role' => 'employee']);

    $this->actingAs($employee)
        ->get(route('inventory.index'))
        ->assertForbidden();

    $this->actingAs($employee)
        ->get(route('staff.index'))
        ->assertForbidden();
});

test('owners can manage inventory and open staff management', function () {
    $this->seed(\Database\Seeders\MenuSeeder::class);
    $owner = User::factory()->create(['role' => 'owner']);
    $inventory = Inventory::create(['item_name' => 'Chicken Breast', 'quantity' => 2]);

    $this->actingAs($owner)
        ->get(route('inventory.index'))
        ->assertOk()
        ->assertSee('Chicken Breast')
        ->assertSee('Coke (Can)')
        ->assertSee('Coca-Cola (Bottle)')
        ->assertDontSee('Cooking oil');

    $this->get(route('pos.index'))
        ->assertOk()
        ->assertSee('Fried Drumsticks (3)')
        ->assertSee('Possin Drumsticks (3)')
        ->assertSee('Masala Drumsticks (3)');

    $this->get(route('staff.index'))->assertOk();

    $this->patch(route('inventory.adjust', $inventory), [
        'action' => 'add',
        'quantity' => 0.5,
    ])->assertRedirect(route('inventory.index'));

    expect((float) $inventory->fresh()->quantity)->toBe(2.5);
});

test('menu orders deduct the configured recipe quantities from measured inventory', function () {
    $user = User::factory()->create(['role' => 'employee']);
    $recipes = [
        ['Chicken Nuggets', 'FISH & CHICKEN BITES', 'Chicken Nuggets', 7],
        ['Fish Fingers', 'FISH & CHICKEN BITES', 'Fish Fingers', 6],
        ['Half Possin Chicken & Chips', 'POSSIN CHICKEN', 'Possin Chicken Portions', 1],
        ['Full Possin Chicken & Chips', 'POSSIN CHICKEN', 'Possin Chicken Portions', 2],
        ['½ Fried Chicken Breast', 'FRIED CHICKEN', 'Chicken Breast', 0.5],
        ['½ Fried Chicken Leg', 'FRIED CHICKEN', 'Fried Chicken Portions', 1],
        ['Half Fried Chicken', 'FRIED CHICKEN', 'Fried Chicken Portions', 1],
        ['Full Fried Chicken', 'FRIED CHICKEN', 'Fried Chicken Portions', 2],
        ['Possin Wings', 'WINGS', 'Wings', 6],
        ['Fried Wings', 'WINGS', 'Wings', 6],
        ['Masala Wings', 'WINGS', 'Wings', 6],
        ['Mabuyu Lollies', 'ICE LOLLIES', 'Ice Lolly', 1],
        ['Choc Sticks', 'ICE LOLLIES', 'Ice Lolly', 1],
        ['Milk Banana Lollies', 'ICE LOLLIES', 'Ice Lolly', 1],
        ['Keema Chapati', 'SNACKS', 'Chapati', 1],
        ['Chicken Chapati', 'SNACKS', 'Chapati', 1],
        ['Chapati', 'SNACKS', 'Chapati', 1],
        ['Sausage', 'SNACKS', 'Sausage', 1],
        ['Mandazi', 'SNACKS', 'Mandazi', 1],
        ['Mutton Samosa', 'SNACKS', 'Mutton Samosa', 4],
        ['Veg Samosa (Per Plate)', 'VEGETERIAN', 'Veg Samosa', 3],
        ['Daal Bhajia (Per Plate)', 'VEGETERIAN', 'Daal Bhajia', 6],
        ['Fried Drumsticks (3)', 'DRUMSTICKS', 'Drumsticks', 3],
        ['Possin Drumsticks (3)', 'DRUMSTICKS', 'Drumsticks', 3],
        ['Masala Drumsticks (3)', 'DRUMSTICKS', 'Drumsticks', 3],
        ['Coke (Can)', 'SOFT DRINKS (CAN)', 'Coke (Can)', 1],
        ['Coca-Cola (Bottle)', 'SOFT DRINKS (BOTTLE)', 'Coca-Cola (Bottle)', 1],
    ];

    $cart = [];
    foreach ($recipes as [$name, $category, $inventoryName, $deductPerItem]) {
        $menuItem = Item::create([
            'name' => $name,
            'category' => $category,
            'price' => 100,
            'stock' => 10,
        ]);
        $cart[] = ['id' => $menuItem->id, 'quantity' => 1];
        Inventory::firstOrCreate(
            ['item_name' => $inventoryName],
            ['quantity' => 100],
        );
    }

    $this->actingAs($user)->post(route('orders.store'), [
        'order_type' => 'Dine-in',
        'customer_name' => 'Customer',
        'payment_status' => 'paid',
        'cart' => $cart,
    ])->assertSessionHasNoErrors();

    foreach ([
        'Chicken Nuggets' => 93,
        'Fish Fingers' => 94,
        'Possin Chicken Portions' => 97,
        'Chicken Breast' => 99.5,
        'Fried Chicken Portions' => 96,
        'Wings' => 82,
        'Ice Lolly' => 97,
        'Chapati' => 97,
        'Sausage' => 99,
        'Mandazi' => 99,
        'Mutton Samosa' => 96,
        'Veg Samosa' => 97,
        'Daal Bhajia' => 94,
        'Drumsticks' => 91,
        'Coke (Can)' => 99,
        'Coca-Cola (Bottle)' => 99,
    ] as $inventoryName => $expectedQuantity) {
        expect((float) Inventory::where('item_name', $inventoryName)->value('quantity'))
            ->toBe((float) $expectedQuantity);
    }
});
