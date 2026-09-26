<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // General
            ['group' => 'general', 'key' => 'store_name', 'value' => 'Shoping Website', 'type' => 'text'],
            ['group' => 'general', 'key' => 'store_email', 'value' => 'store@example.com', 'type' => 'text'],
            ['group' => 'general', 'key' => 'store_phone', 'value' => '0300-0000000', 'type' => 'text'],
            ['group' => 'general', 'key' => 'store_address', 'value' => 'Pakistan', 'type' => 'text'],

            // Currency
            ['group' => 'currency', 'key' => 'currency_symbol', 'value' => 'Rs.', 'type' => 'text'],
            ['group' => 'currency', 'key' => 'currency_code', 'value' => 'PKR', 'type' => 'text'],

            // Shipping
            ['group' => 'shipping', 'key' => 'shipping_flat_rate', 'value' => '200', 'type' => 'number'],
            ['group' => 'shipping', 'key' => 'shipping_free_over', 'value' => '5000', 'type' => 'number'],
            ['group' => 'shipping', 'key' => 'shipping_rate_per_kg', 'value' => '100', 'type' => 'number'],

            // Tax
            ['group' => 'tax', 'key' => 'tax_rate', 'value' => '0', 'type' => 'number'],
            ['group' => 'tax', 'key' => 'tax_included', 'value' => '0', 'type' => 'bool'],

            ['group' => 'general', 'key' => 'logo', 'value' => null, 'type' => 'image'],
            ['group' => 'general', 'key' => 'favicon', 'value' => null, 'type' => 'image'],
            ['group' => 'general', 'key' => 'whatsapp_enabled', 'value' => '0', 'type' => 'bool'],
            ['group' => 'general', 'key' => 'whatsapp_number', 'value' => '', 'type' => 'text'],
            ['group' => 'general', 'key' => 'whatsapp_message', 'value' => 'Hello, I have a question!', 'type' => 'text'],

            ['group' => 'seo', 'key' => 'meta_title', 'value' => 'Online Shopping Store', 'type' => 'text'],
            ['group' => 'seo', 'key' => 'meta_description', 'value' => 'Buy quality products online at best prices.', 'type' => 'text'],
            ['group' => 'seo', 'key' => 'meta_keywords', 'value' => 'shop, ecommerce, buy online', 'type' => 'text'],
            ['group' => 'seo', 'key' => 'google_analytics_id', 'value' => '', 'type' => 'text'],
        ];

        foreach ($defaults as $s) {
            Setting::updateOrCreate(['key' => $s['key']], $s);
        }
    }
}