<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultCategories = [
            [
                'name' => 'Office Supplies',
                'icon' => 'pencil-ruler',
                'color' => '#3B82F6',
                'is_default' => true,
                'user_id' => null,
            ],
            [
                'name' => 'Food & Beverages',
                'icon' => 'utensils',
                'color' => '#F97316',
                'is_default' => true,
                'user_id' => null,
            ],
            [
                'name' => 'Transportation',
                'icon' => 'car',
                'color' => '#8B5CF6',
                'is_default' => true,
                'user_id' => null,
            ],
            [
                'name' => 'Miscellaneous',
                'icon' => 'box',
                'color' => '#6B7280',
                'is_default' => true,
                'user_id' => null,
            ],
            [
                'name' => 'Other',
                'icon' => 'more-horizontal',
                'color' => '#9CA3AF',
                'is_default' => true,
                'user_id' => null,
            ],
        ];

        foreach ($defaultCategories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name'], 'is_default' => true],
                $category
            );
        }
    }
}
