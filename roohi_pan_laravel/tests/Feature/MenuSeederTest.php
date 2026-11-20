<?php

use App\Models\Item;
use Database\Seeders\MenuSeeder;

test('menu seeder creates all menu items without duplicating them', function () {
    $this->seed(MenuSeeder::class);
    $this->seed(MenuSeeder::class);

    expect(Item::query()->count())->toBe(66)
        ->and((float) Item::query()->where('name', 'Vanilla Faluda')->value('price'))->toBe(400.0);
});
