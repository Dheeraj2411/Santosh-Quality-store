<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\Role;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            StoreSettingSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
