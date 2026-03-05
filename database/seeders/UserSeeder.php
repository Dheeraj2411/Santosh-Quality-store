<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole    = Role::where('name', 'admin')->first();
        $customerRole = Role::where('name', 'customer')->first();

        // Admin User
        User::firstOrCreate(
            ['email' => 'admin@santoshstore.com'],
            [
                'name'              => 'Santosh Admin',
                'password'          => Hash::make('password123'),
                'role_id'           => $adminRole->id,
                'phone'             => '+91 96506 71568',
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );

        // Demo Customers
        $customers = [
            ['name' => 'Amit Sharma',    'email' => 'amit@example.com'],
            ['name' => 'Priya Singh',    'email' => 'priya@example.com'],
            ['name' => 'Rahul Verma',    'email' => 'rahul@example.com'],
            ['name' => 'Sunita Gupta',   'email' => 'sunita@example.com'],
            ['name' => 'Vikram Patel',   'email' => 'vikram@example.com'],
            ['name' => 'Meena Yadav',    'email' => 'meena@example.com'],
            ['name' => 'Rajesh Kumar',   'email' => 'rajesh@example.com'],
            ['name' => 'Anita Joshi',    'email' => 'anita@example.com'],
            ['name' => 'Deepak Nair',    'email' => 'deepak@example.com'],
            ['name' => 'Kavita Rao',     'email' => 'kavita@example.com'],
        ];

        foreach ($customers as $customerData) {
            User::firstOrCreate(
                ['email' => $customerData['email']],
                [
                    'name'              => $customerData['name'],
                    'password'          => Hash::make('password123'),
                    'role_id'           => $customerRole->id,
                    'is_active'         => true,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
