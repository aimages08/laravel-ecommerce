<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        Banner::firstOrCreate(['type' => 'hero'], [
            'title' => 'Browse Our Premium Products',
            'subtitle' => 'Shop is fun',
            'description' => 'Discover top-quality items at great prices. Free shipping over Rs. 5,000.',
            'button_text' => 'Browse Now',
            'button_url' => '/shop/products',
            'image' => 'https://picsum.photos/seed/hero-shop/700/500',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        Banner::firstOrCreate(['type' => 'promo'], [
            'title' => 'Up To 50% Off',
            'subtitle' => 'Winter Sale',
            'description' => 'Limited time offer — grab your favorites before they\'re gone.',
            'button_text' => 'Shop Now',
            'button_url' => '/shop/products?sort=price_asc',
            'image' => 'https://picsum.photos/seed/winter-sale/500/400',
            'is_active' => true,
            'sort_order' => 0,
        ]);
    }
}