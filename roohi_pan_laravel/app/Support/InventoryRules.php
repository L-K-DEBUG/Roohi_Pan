<?php

namespace App\Support;

use App\Models\Item;

final class InventoryRules
{
    private const ITEM_DEDUCTIONS = [
        'Chicken Nuggets' => ['Chicken Nuggets', 7],
        'Fish Fingers' => ['Fish Fingers', 6],
        'Half Possin Chicken & Chips' => ['Possin Chicken Portions', 1],
        'Full Possin Chicken & Chips' => ['Possin Chicken Portions', 2],
        '½ Fried Chicken Breast' => ['Chicken Breast', 0.5],
        '½ Fried Chicken Leg' => ['Fried Chicken Portions', 1],
        'Half Fried Chicken' => ['Fried Chicken Portions', 1],
        'Full Fried Chicken' => ['Fried Chicken Portions', 2],
        'Possin Wings' => ['Wings', 6],
        'Fried Wings' => ['Wings', 6],
        'Masala Wings' => ['Wings', 6],
        'Mabuyu Lollies' => ['Ice Lolly', 1],
        'Choc Sticks' => ['Ice Lolly', 1],
        'Milk Banana Lollies' => ['Ice Lolly', 1],
        'Keema Chapati' => ['Chapati', 1],
        'Chicken Chapati' => ['Chapati', 1],
        'Chapati' => ['Chapati', 1],
        'Sausage' => ['Sausage', 1],
        'Mandazi' => ['Mandazi', 1],
        'Mutton Samosa' => ['Mutton Samosa', 4],
        'Veg Samosa (Per Plate)' => ['Veg Samosa', 3],
        'Daal Bhajia (Per Plate)' => ['Daal Bhajia', 6],
        'Fried Drumsticks (3)' => ['Drumsticks', 3],
        'Possin Drumsticks (3)' => ['Drumsticks', 3],
        'Masala Drumsticks (3)' => ['Drumsticks', 3],
    ];

    private const INGREDIENT_GROUPS = [
        'Chicken' => [
            'Chicken Nuggets',
            'Fish Fingers',
            'Possin Chicken Portions',
            'Fried Chicken Portions',
        ],
        'Chicken Breast' => ['Chicken Breast'],
        'Drumsticks' => ['Drumsticks'],
        'Wings' => ['Wings'],
        'Ice Lollies' => ['Ice Lolly'],
        'Chapati' => ['Chapati'],
        'Sausages' => ['Sausage'],
        'Mandazi' => ['Mandazi'],
        'Mutton Samosas' => ['Mutton Samosa'],
        'Vegetable Samosas' => ['Veg Samosa'],
        'Daal Bhajia' => ['Daal Bhajia'],
    ];

    public static function deductionsFor(Item $item): array
    {
        if (isset(self::ITEM_DEDUCTIONS[$item->name])) {
            [$inventoryName, $quantity] = self::ITEM_DEDUCTIONS[$item->name];

            return [['item_name' => $inventoryName, 'quantity' => $quantity]];
        }

        if (str_starts_with($item->category, 'SOFT DRINKS')) {
            return [['item_name' => $item->name, 'quantity' => 1]];
        }

        return [];
    }

    public static function ingredientGroups(): array
    {
        return self::INGREDIENT_GROUPS;
    }
}
