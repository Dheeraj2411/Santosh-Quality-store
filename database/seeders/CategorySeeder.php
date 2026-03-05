<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Dal & Pulses',      'emoji' => '🫘', 'sort_order' => 1],
            ['name' => 'Rice & Grains',     'emoji' => '🌾', 'sort_order' => 2],
            ['name' => 'Spices & Masala',   'emoji' => '🌶️', 'sort_order' => 3],
            ['name' => 'Oil & Ghee',        'emoji' => '🫙', 'sort_order' => 4],
            ['name' => 'Snacks & Namkeen',  'emoji' => '🍟', 'sort_order' => 5],
            ['name' => 'Beverages',         'emoji' => '☕', 'sort_order' => 6],
            ['name' => 'Dairy & Paneer',    'emoji' => '🥛', 'sort_order' => 7],
            ['name' => 'Atta & Flour',      'emoji' => '🌽', 'sort_order' => 8],
            ['name' => 'Sugar & Salt',      'emoji' => '🧂', 'sort_order' => 9],
            ['name' => 'Personal Care',     'emoji' => '🧴', 'sort_order' => 10],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['name' => $cat['name']],
                [
                    'description' => $cat['emoji'] . ' Fresh ' . $cat['name'] . ' available daily',
                    'is_active'   => true,
                    'sort_order'  => $cat['sort_order'],
                ]
            );
        }
    }
}
