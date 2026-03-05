<?php

namespace Database\Seeders;

use App\Models\StoreSetting;
use Illuminate\Database\Seeder;

class StoreSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'store_name'          => 'संतोष स्टोर (Santosh Store)',
            'store_tagline'       => 'Your trusted neighbourhood grocery store since 1995',
            'address'             => 'Kirti Nagar, Sector 15 Part 1, Sector 15, Gurugram, Haryana 122001, India',
            'phone'               => '+91 96506 71568',
            'email'               => 'contact@santoshstore.com',
            'open_time'           => '06:00',
            'close_time'          => '22:00',
            'delivery_free_above' => '500',
            'delivery_charge'     => '40',
            'plus_code'           => 'F23P+8X Gurugram',
            'google_maps_url'     => 'https://maps.google.com/?q=Kirti+Nagar+Sector+15+Gurugram+Haryana',
            'about_text'          => 'Santosh Store has been serving the residents of Kirti Nagar and Sector 15, Gurugram since 1995. We pride ourselves on offering fresh, high-quality groceries at the best prices. Our store provides in-store shopping, pickup, and home delivery services.',
            'rating'              => '4.0',
            'rating_count'        => '35',
            'services'            => 'In-store shopping, In-store pickup, Home delivery',
        ];

        foreach ($settings as $key => $value) {
            StoreSetting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
