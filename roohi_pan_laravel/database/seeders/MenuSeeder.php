<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Seed the menu items.
     */
    public function run(): void
    {
        $items = [
            ['Vanilla Faluda', 'FALUDA', 400],
            ['Strawberry Faluda', 'FALUDA', 400],
            ['Chocolate Faluda', 'FALUDA', 400],
            ['Pistachio Faluda', 'FALUDA', 500],
            ['Kesar Badam Faluda', 'FALUDA', 500],
            ['Shredded Kulfi (Original)', 'FALUDA', 300],
            ['Vanilla Milkshake', 'MILKSHAKE', 300],
            ['Strawberry Milkshake', 'MILKSHAKE', 300],
            ['Chocolate Milkshake', 'MILKSHAKE', 300],
            ['Pistachio Milkshake', 'MILKSHAKE', 400],
            ['Kesar Badam Milkshake', 'MILKSHAKE', 400],
            ['Kitkat Milkshake', 'MILKSHAKE', 500],
            ['Oreo Milkshake', 'MILKSHAKE', 450],
            ['Coke (Can)', 'SOFT DRINKS (CAN)', 250],
            ['Coke Zero (Can)', 'SOFT DRINKS (CAN)', 250],
            ['Fanta (Can)', 'SOFT DRINKS (CAN)', 250],
            ['Sprite (Can)', 'SOFT DRINKS (CAN)', 250],
            ['Pepsi (Can)', 'SOFT DRINKS (CAN)', 250],
            ['Mirinda (Can)', 'SOFT DRINKS (CAN)', 250],
            ['Mountain Dew (Can)', 'SOFT DRINKS (CAN)', 250],
            ['7UP (Can)', 'SOFT DRINKS (CAN)', 250],
            ['Lemonade (Can)', 'SOFT DRINKS (CAN)', 250],
            ['Coca-Cola (Bottle)', 'SOFT DRINKS (BOTTLE)', 60],
            ['Coke Zero (Bottle)', 'SOFT DRINKS (BOTTLE)', 60],
            ['Fanta (Bottle)', 'SOFT DRINKS (BOTTLE)', 60],
            ['Sprite (Bottle)', 'SOFT DRINKS (BOTTLE)', 60],
            ['Pepsi (Bottle)', 'SOFT DRINKS (BOTTLE)', 60],
            ['Mirinda (Bottle)', 'SOFT DRINKS (BOTTLE)', 60],
            ['Mountain Dew (Bottle)', 'SOFT DRINKS (BOTTLE)', 60],
            ['7UP (Bottle)', 'SOFT DRINKS (BOTTLE)', 60],
            ['Lemonade (Bottle)', 'SOFT DRINKS (BOTTLE)', 60],
            ['Mabuyu Lollies', 'ICE LOLLIES', 60],
            ['Choc Sticks', 'ICE LOLLIES', 80],
            ['Milk Banana Lollies', 'ICE LOLLIES', 80],
            ['Mutton Samosa', 'SNACKS', 350],
            ['Keema Chapati', 'SNACKS', 350],
            ['Chicken Chapati', 'SNACKS', 300],
            ['Hot Dogs', 'SNACKS', 200],
            ['Sausage', 'SNACKS', 65],
            ['Mandazi', 'SNACKS', 30],
            ['Chapati', 'SNACKS', 30],
            ['Chips', 'SNACKS', 150],
            ['Tea Special', 'HOT BEVERAGES', 150],
            ['Black Tea', 'HOT BEVERAGES', 50],
            ['Black Coffee', 'HOT BEVERAGES', 150],
            ['White Coffee', 'HOT BEVERAGES', 200],
            ['Plain Chips', 'FRIES', 180],
            ['Masala Chips', 'FRIES', 250],
            ['Possin Chips', 'FRIES', 300],
            ['Veg Samosa (Per Plate)', 'VEGETERIAN', 300],
            ['Maru Bhajia (Per Plate)', 'VEGETERIAN', 350],
            ['Daal Bhajia (Per Plate)', 'VEGETERIAN', 250],
            ['Fish Fingers', 'FISH & CHICKEN BITES', 450],
            ['Chicken Nuggets', 'FISH & CHICKEN BITES', 450],
            ['Half Possin Chicken & Chips', 'POSSIN CHICKEN', 700],
            ['Full Possin Chicken & Chips', 'POSSIN CHICKEN', 1200],
            ['½ Fried Chicken Breast', 'FRIED CHICKEN', 350],
            ['½ Fried Chicken Leg', 'FRIED CHICKEN', 300],
            ['Half Fried Chicken', 'FRIED CHICKEN', 600],
            ['Full Fried Chicken', 'FRIED CHICKEN', 1000],
            ['Possin Wings', 'WINGS', 500],
            ['Fried Wings', 'WINGS', 350],
            ['Masala Wings', 'WINGS', 450],
            ['Fried Drumsticks (3)', 'DRUMSTICKS', 350],
            ['Possin Drumsticks (3)', 'DRUMSTICKS', 500],
            ['Masala Drumsticks (3)', 'DRUMSTICKS', 450],
        ];

        foreach ($items as [$name, $category, $price]) {
            Item::firstOrCreate(
                ['name' => $name],
                ['category' => $category, 'price' => $price, 'stock' => 50],
            );
        }
    }
}
