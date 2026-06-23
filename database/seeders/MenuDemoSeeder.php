<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuSection;
use App\Models\MenuCategory;
use App\Models\MenuSubcategory;
use App\Models\MenuItem;
use App\Models\Tag;

class MenuDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Tags
        $spicy = Tag::create(['name' => 'Spicy', 'color' => '#dc3545']);
        $vegan = Tag::create(['name' => 'Vegan', 'color' => '#28a745']);
        $new = Tag::create(['name' => 'New', 'color' => '#007bff']);

        // Section
        $foodSection = MenuSection::create([
            'name' => 'Food Menu',
            'description' => 'Delicious main courses and starters',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $drinkSection = MenuSection::create([
            'name' => 'Beverages',
            'description' => 'Hot and cold drinks',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        // Categories
        $starters = MenuCategory::create([
            'section_id' => $foodSection->id,
            'name' => 'Starters',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $mains = MenuCategory::create([
            'section_id' => $foodSection->id,
            'name' => 'Main Courses',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $softDrinks = MenuCategory::create([
            'section_id' => $drinkSection->id,
            'name' => 'Soft Drinks',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // Subcategories
        $beef = MenuSubcategory::create([
            'category_id' => $mains->id,
            'name' => 'Beef',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $chicken = MenuSubcategory::create([
            'category_id' => $mains->id,
            'name' => 'Chicken',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        // Items
        $item1 = MenuItem::create([
            'category_id' => $starters->id,
            'name' => 'Spicy Wings',
            'description' => 'Crispy wings with spicy sauce',
            'price' => 8.99,
            'sort_order' => 1,
            'is_available' => true,
        ]);
        $item1->tags()->sync([$spicy->id, $new->id]);

        $item2 = MenuItem::create([
            'category_id' => $mains->id,
            'subcategory_id' => $beef->id,
            'name' => 'Ribeye Steak',
            'description' => '12oz prime ribeye with garlic butter',
            'price' => 29.99,
            'sort_order' => 1,
            'is_available' => true,
        ]);

        $item3 = MenuItem::create([
            'category_id' => $mains->id,
            'name' => 'Vegan Burger',
            'description' => 'Plant-based patty with fresh veggies',
            'price' => 14.99,
            'sort_order' => 2,
            'is_available' => true,
        ]);
        $item3->tags()->sync([$vegan->id]);
    }
}
