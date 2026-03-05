<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Dal & Pulses
            ['category' => 'Dal & Pulses', 'name' => 'Toor Dal (Arhar)',         'price' => 140, 'mrp' => 160, 'unit' => 'kg',   'stock' => 50,  'featured' => true],
            ['category' => 'Dal & Pulses', 'name' => 'Moong Dal (Split Green)',  'price' => 120, 'mrp' => 140, 'unit' => 'kg',   'stock' => 40,  'featured' => false],
            ['category' => 'Dal & Pulses', 'name' => 'Chana Dal (Bengal Gram)',  'price' => 110, 'mrp' => 130, 'unit' => 'kg',   'stock' => 35,  'featured' => false],
            ['category' => 'Dal & Pulses', 'name' => 'Masoor Dal (Red Lentil)', 'price' => 100, 'mrp' => 120, 'unit' => 'kg',   'stock' => 60,  'featured' => false],
            ['category' => 'Dal & Pulses', 'name' => 'Urad Dal (Black Lentil)', 'price' => 130, 'mrp' => 150, 'unit' => 'kg',   'stock' => 30,  'featured' => true],
            ['category' => 'Dal & Pulses', 'name' => 'Rajma (Red Kidney Beans)', 'price' => 160, 'mrp' => 180, 'unit' => 'kg', 'stock' => 25,  'featured' => false],

            // Rice & Grains
            ['category' => 'Rice & Grains', 'name' => 'Basmati Rice (Premium)',  'price' => 280, 'mrp' => 320, 'unit' => 'kg', 'stock' => 100, 'featured' => true],
            ['category' => 'Rice & Grains', 'name' => 'Sona Masoori Rice',       'price' => 65,  'mrp' => 80,  'unit' => 'kg', 'stock' => 80,  'featured' => false],
            ['category' => 'Rice & Grains', 'name' => 'Brown Rice (Whole Grain)','price' => 95,  'mrp' => 110, 'unit' => 'kg', 'stock' => 40,  'featured' => false],
            ['category' => 'Rice & Grains', 'name' => 'Poha (Flattened Rice)',   'price' => 55,  'mrp' => 70,  'unit' => 'kg', 'stock' => 50,  'featured' => false],
            ['category' => 'Rice & Grains', 'name' => 'Suji / Rava (Semolina)', 'price' => 48,  'mrp' => 60,  'unit' => 'kg', 'stock' => 45,  'featured' => false],

            // Spices & Masala
            ['category' => 'Spices & Masala', 'name' => 'Haldi (Turmeric Powder)',    'price' => 85,  'mrp' => 100, 'unit' => '500g', 'stock' => 60,  'featured' => true],
            ['category' => 'Spices & Masala', 'name' => 'Lal Mirch (Red Chilli Powder)', 'price' => 95, 'mrp' => 110, 'unit' => '500g', 'stock' => 55, 'featured' => false],
            ['category' => 'Spices & Masala', 'name' => 'Dhaniya Powder (Coriander)',  'price' => 75,  'mrp' => 90,  'unit' => '500g', 'stock' => 50,  'featured' => false],
            ['category' => 'Spices & Masala', 'name' => 'Garam Masala (Blend)',        'price' => 130, 'mrp' => 150, 'unit' => '500g', 'stock' => 40,  'featured' => true],
            ['category' => 'Spices & Masala', 'name' => 'Jeera (Cumin Seeds)',        'price' => 110, 'mrp' => 130, 'unit' => '500g', 'stock' => 45,  'featured' => false],
            ['category' => 'Spices & Masala', 'name' => 'Rai (Mustard Seeds)',        'price' => 60,  'mrp' => 75,  'unit' => '500g', 'stock' => 50,  'featured' => false],
            ['category' => 'Spices & Masala', 'name' => 'Hing (Asafoetida)',          'price' => 180, 'mrp' => 200, 'unit' => '50g',  'stock' => 30,  'featured' => false],

            // Oil & Ghee
            ['category' => 'Oil & Ghee', 'name' => 'Patanjali Ghee (Pure)',       'price' => 560, 'mrp' => 620, 'unit' => 'kg',       'stock' => 30,  'featured' => true],
            ['category' => 'Oil & Ghee', 'name' => 'Fortune Sunflower Oil',       'price' => 145, 'mrp' => 165, 'unit' => 'litre',    'stock' => 50,  'featured' => false],
            ['category' => 'Oil & Ghee', 'name' => 'Saffola Gold Oil (2L)',       'price' => 285, 'mrp' => 320, 'unit' => '2L',       'stock' => 40,  'featured' => true],
            ['category' => 'Oil & Ghee', 'name' => 'Til (Sesame) Oil',            'price' => 220, 'mrp' => 250, 'unit' => 'litre',    'stock' => 20,  'featured' => false],
            ['category' => 'Oil & Ghee', 'name' => 'Mustard Oil (Kachi Ghani)',   'price' => 180, 'mrp' => 200, 'unit' => 'litre',    'stock' => 35,  'featured' => false],

            // Snacks & Namkeen
            ['category' => 'Snacks & Namkeen', 'name' => 'Haldiram Bhujia (500g)',      'price' => 115, 'mrp' => 130, 'unit' => 'pack', 'stock' => 40,  'featured' => true],
            ['category' => 'Snacks & Namkeen', 'name' => 'Bikanerwala Aloo Bhujia',     'price' => 95,  'mrp' => 110, 'unit' => 'pack', 'stock' => 35,  'featured' => false],
            ['category' => 'Snacks & Namkeen', 'name' => 'Kurkure Masala Munch (4 Pk)', 'price' => 60,  'mrp' => 75,  'unit' => 'pack', 'stock' => 60,  'featured' => false],
            ['category' => 'Snacks & Namkeen', 'name' => 'Parle-G Biscuits (800g)',     'price' => 75,  'mrp' => 85,  'unit' => 'pack', 'stock' => 80,  'featured' => false],
            ['category' => 'Snacks & Namkeen', 'name' => 'Good Day Cashew Cookies',     'price' => 55,  'mrp' => 65,  'unit' => 'pack', 'stock' => 55,  'featured' => false],
            ['category' => 'Snacks & Namkeen', 'name' => 'Maggi Noodles (12 Pack)',     'price' => 130, 'mrp' => 150, 'unit' => 'pack', 'stock' => 70,  'featured' => true],

            // Beverages
            ['category' => 'Beverages', 'name' => 'Tata Tea Gold (500g)',           'price' => 265, 'mrp' => 300, 'unit' => 'pack', 'stock' => 40,  'featured' => true],
            ['category' => 'Beverages', 'name' => 'Nescafé Classic Coffee (200g)',  'price' => 450, 'mrp' => 510, 'unit' => 'pack', 'stock' => 25,  'featured' => false],
            ['category' => 'Beverages', 'name' => 'Bournvita (1kg)',                'price' => 520, 'mrp' => 580, 'unit' => 'pack', 'stock' => 30,  'featured' => true],
            ['category' => 'Beverages', 'name' => 'Rooh Afza (750ml)',              'price' => 185, 'mrp' => 210, 'unit' => 'bottle','stock' => 35,  'featured' => false],
            ['category' => 'Beverages', 'name' => 'Rasna Powder (Mango)',           'price' => 75,  'mrp' => 90,  'unit' => 'pack', 'stock' => 50,  'featured' => false],

            // Dairy & Paneer
            ['category' => 'Dairy & Paneer', 'name' => 'Amul Butter (500g)',         'price' => 280, 'mrp' => 300, 'unit' => 'pack', 'stock' => 30,  'featured' => true],
            ['category' => 'Dairy & Paneer', 'name' => 'Mother Dairy Paneer (200g)', 'price' => 95,  'mrp' => 110, 'unit' => 'piece','stock' => 20,  'featured' => false],
            ['category' => 'Dairy & Paneer', 'name' => 'Nestle Dahi (400g)',         'price' => 55,  'mrp' => 65,  'unit' => 'piece','stock' => 40,  'featured' => false],

            // Atta & Flour
            ['category' => 'Atta & Flour', 'name' => 'Aashirvaad Atta (10kg)',    'price' => 480, 'mrp' => 550, 'unit' => '10kg', 'stock' => 40,  'featured' => true],
            ['category' => 'Atta & Flour', 'name' => 'Fortune Chakki Fresh Atta (5kg)', 'price' => 250, 'mrp' => 285, 'unit' => '5kg', 'stock' => 50, 'featured' => false],
            ['category' => 'Atta & Flour', 'name' => 'Besan (Chickpea Flour)',    'price' => 95,  'mrp' => 115, 'unit' => 'kg',   'stock' => 45,  'featured' => false],
            ['category' => 'Atta & Flour', 'name' => 'Maida (All Purpose Flour)', 'price' => 45,  'mrp' => 55,  'unit' => 'kg',   'stock' => 60,  'featured' => false],

            // Sugar & Salt
            ['category' => 'Sugar & Salt', 'name' => 'Sugar (Cheeni - 5kg)',      'price' => 220, 'mrp' => 245, 'unit' => '5kg', 'stock' => 60,  'featured' => false],
            ['category' => 'Sugar & Salt', 'name' => 'Tata Salt (1kg)',            'price' => 28,  'mrp' => 35,  'unit' => 'kg',  'stock' => 100, 'featured' => true],
            ['category' => 'Sugar & Salt', 'name' => 'Gud (Jaggery - 500g)',      'price' => 65,  'mrp' => 80,  'unit' => '500g','stock' => 40,  'featured' => false],
            ['category' => 'Sugar & Salt', 'name' => 'Mishri (Rock Sugar)',        'price' => 85,  'mrp' => 100, 'unit' => '500g','stock' => 25,  'featured' => false],

            // Personal Care
            ['category' => 'Personal Care', 'name' => 'Patanjali Dant Kanti (200g)',    'price' => 95,  'mrp' => 110, 'unit' => 'piece','stock' => 40, 'featured' => false],
            ['category' => 'Personal Care', 'name' => 'Colgate Strong Teeth Paste',     'price' => 85,  'mrp' => 100, 'unit' => 'piece','stock' => 50, 'featured' => false],
            ['category' => 'Personal Care', 'name' => 'Lux Soap (75g × 4)',             'price' => 120, 'mrp' => 140, 'unit' => 'pack', 'stock' => 45, 'featured' => false],
            ['category' => 'Personal Care', 'name' => 'Neem Tulsi Soap (Herbal)',       'price' => 65,  'mrp' => 80,  'unit' => 'piece','stock' => 30, 'featured' => false],
        ];

        foreach ($products as $productData) {
            $category = Category::where('name', $productData['category'])->first();
            if (!$category) continue;

            $description = "Premium quality {$productData['name']} available at Santosh Store, Gurugram. " .
                "Fresh stock, best prices in Kirti Nagar. Perfect for your daily grocery needs.";

            Product::firstOrCreate(
                ['name' => $productData['name']],
                [
                    'category_id' => $category->id,
                    'slug'        => Str::slug($productData['name']),
                    'description' => $description,
                    'price'       => $productData['price'],
                    'mrp'         => $productData['mrp'],
                    'unit'        => $productData['unit'],
                    'stock'       => $productData['stock'],
                    'is_featured' => $productData['featured'],
                    'is_active'   => true,
                ]
            );
        }
    }
}
