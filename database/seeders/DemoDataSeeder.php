<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- CATEGORIES ----------
        $categories = [
            'Electronics' => 'Mobiles, laptops, accessories',
            'Fashion'     => 'Men, women, kids clothing',
            'Home & Decor'=> 'Furniture, decor, kitchen',
            'Beauty'      => 'Skincare, makeup, fragrance',
            'Sports'      => 'Fitness, outdoor, gym',
            'Books'       => 'Fiction, education, comics',
        ];

        $catModels = [];
        foreach ($categories as $name => $desc) {
            $catModels[$name] = Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'description' => $desc, 'is_active' => true]
            );
        }

        // ---------- BRANDS ----------
        $brands = ['Apple', 'Samsung', 'Nike', 'Adidas', 'Sony', 'Xiaomi', 'Canon', 'Loreal'];
        $brandModels = [];
        foreach ($brands as $b) {
            $brandModels[$b] = Brand::firstOrCreate(
                ['slug' => Str::slug($b)],
                ['name' => $b, 'is_active' => true]
            );
        }

        // ---------- PRODUCTS ----------
        // [name, category, brand, price, sale_price, stock, featured, shipping_type]
        $products = [
            ['iPhone 15 Pro', 'Electronics', 'Apple', 320000, 299999, 15, true, 'flat'],
            ['Galaxy S24 Ultra', 'Electronics', 'Samsung', 280000, null, 20, true, 'flat'],
            ['AirPods Pro', 'Electronics', 'Apple', 55000, 52000, 40, true, 'flat'],
            ['Redmi Note 13', 'Electronics', 'Xiaomi', 45000, 42000, 50, false, 'flat'],
            ['Bravia 55" TV', 'Electronics', 'Sony', 185000, 175000, 8, true, 'flat'],
            ['Canon EOS R50', 'Electronics', 'Canon', 220000, null, 5, false, 'flat'],

            ['Air Max 270', 'Fashion', 'Nike', 25000, 22000, 30, true, 'global'],
            ['Ultraboost 22', 'Fashion', 'Adidas', 27000, 24000, 25, false, 'global'],
            ['Cotton Hoodie', 'Fashion', 'Nike', 8500, 7500, 100, true, 'free'],
            ['Denim Jacket', 'Fashion', 'Adidas', 12000, null, 40, false, 'global'],

            ['Modern Sofa', 'Home & Decor', null, 85000, 78000, 5, true, 'per_kg'],
            ['Wooden Table', 'Home & Decor', null, 35000, null, 10, false, 'per_kg'],
            ['Table Lamp', 'Home & Decor', null, 4500, 3900, 30, false, 'global'],
            ['Wall Clock', 'Home & Decor', null, 2500, null, 60, false, 'free'],

            ['Face Serum', 'Beauty', 'Loreal', 3200, 2800, 80, true, 'free'],
            ['Lipstick Set', 'Beauty', 'Loreal', 4500, null, 50, false, 'free'],
            ['Perfume 50ml', 'Beauty', null, 6500, 5500, 40, true, 'global'],

            ['Yoga Mat', 'Sports', 'Nike', 3500, null, 70, false, 'free'],
            ['Dumbbell Set', 'Sports', null, 15000, 13500, 20, true, 'per_kg'],
            ['Cricket Bat', 'Sports', 'Adidas', 18000, null, 15, false, 'per_kg'],

            ['Laravel Guide', 'Books', null, 1500, null, 100, true, 'free'],
            ['Clean Code', 'Books', null, 2200, 1999, 80, false, 'free'],
            ['Atomic Habits', 'Books', null, 1800, 1600, 90, true, 'free'],
        ];

        foreach ($products as $p) {
            [$name, $cat, $brand, $price, $sale, $stock, $featured, $shippingType] = $p;

            // Shipping fee / weight / rate based on type
            $shippingFee = $shippingType === 'flat' ? 300 : null;
            $weight      = $shippingType === 'per_kg' ? rand(1, 5) : null;
            $ratePerKg   = $shippingType === 'per_kg' ? 150 : null;

            $product = Product::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name'                  => $name,
                    'category_id'           => $catModels[$cat]->id,
                    'brand_id'              => $brand ? $brandModels[$brand]->id : null,
                    'description'           => "High-quality {$name}. Perfect for everyday use with premium finish and reliable performance.",
                    'price'                 => $price,
                    'sale_price'            => $sale,
                    'cost_price'            => round($price * 0.7),
                    'tax_rate'              => 0,
                    'stock'                 => $stock,
                    'low_stock_threshold'   => 5,
                    'shipping_type'         => $shippingType,
                    'shipping_fee'          => $shippingFee,
                    'shipping_rate_per_kg'  => $ratePerKg,
                    'weight'                => $weight,
                    'is_featured'           => $featured,
                    'is_new'                => true,
                    'is_active'             => true,
                    'status'                => 'published',
                    'meta_title'            => $name,
                    'meta_description'      => "Buy {$name} online at best price.",
                ]
            );

            // ---------- IMAGES (4 per product using picsum) ----------
            if ($product->images()->count() === 0) {
                $seedBase = Str::slug($name);
                for ($i = 1; $i <= 4; $i++) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'path'       => "https://picsum.photos/seed/{$seedBase}-{$i}/800/800",
                        'type'       => 'image',
                        'is_primary' => $i === 1,
                        'sort_order' => $i,
                    ]);
                }
            }
        }

        $this->command->info('Demo data seeded successfully.');
    }
}