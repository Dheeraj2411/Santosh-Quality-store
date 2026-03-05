<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::whereHas('role', fn($q) => $q->where('name', 'customer'))->get();
        $products  = Product::limit(20)->get();

        $reviewTexts = [
            ['title' => 'Great quality!',      'body' => 'Very fresh and good quality products. Happy with my purchase.'],
            ['title' => 'Value for money',     'body' => 'Best prices in the area. Will definitely order again.'],
            ['title' => 'Fast delivery',       'body' => 'Delivered within 2 hours. Packaging was excellent.'],
            ['title' => 'Highly recommended',  'body' => 'Santosh Store never disappoints. Fresh stock always available.'],
            ['title' => 'Good experience',     'body' => 'Easy ordering process and good quality products.'],
            ['title' => 'Fresh products',      'body' => 'Always get fresh dal and rice from here. Trust this store.'],
        ];

        $customerIndex = 0;
        foreach ($products as $product) {
            $numReviews = rand(1, 3);
            for ($i = 0; $i < $numReviews; $i++) {
                $customer = $customers[$customerIndex % count($customers)];
                $reviewText = $reviewTexts[array_rand($reviewTexts)];

                $exists = Review::where('user_id', $customer->id)
                    ->where('product_id', $product->id)
                    ->exists();

                if (!$exists) {
                    Review::create([
                        'user_id'    => $customer->id,
                        'product_id' => $product->id,
                        'rating'     => rand(4, 5),
                        'title'      => $reviewText['title'],
                        'body'       => $reviewText['body'],
                        'is_approved' => true,
                    ]);
                }
                $customerIndex++;
            }
        }
    }
}
