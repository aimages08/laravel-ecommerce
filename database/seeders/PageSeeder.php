<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => '<p>Welcome to our store! We offer quality products at competitive prices.</p><p>Our mission is to deliver the best shopping experience to our customers.</p>',
                'sort_order' => 1,
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => '<p>We respect your privacy. We collect only the information necessary to process your orders.</p>',
                'sort_order' => 2,
            ],
            [
                'title' => 'Terms & Conditions',
                'slug' => 'terms-conditions',
                'content' => '<p>By using this website, you agree to our terms and conditions.</p>',
                'sort_order' => 3,
            ],
            [
                'title' => 'Return Policy',
                'slug' => 'return-policy',
                'content' => '<p>We accept returns within 7 days of delivery. Item must be in original condition.</p>',
                'sort_order' => 4,
            ],
            [
                'title' => 'Shipping Policy',
                'slug' => 'shipping-policy',
                'content' => '<p>We ship within 2-5 business days. Free shipping over Rs. 5000.</p>',
                'sort_order' => 5,
            ],
        ];

        foreach ($pages as $p) {
            Page::firstOrCreate(['slug' => $p['slug']], $p);
        }
    }
}