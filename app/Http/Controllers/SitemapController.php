<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Page;

class SitemapController extends Controller
{
    public function index()
    {
        $products   = Product::where('is_active', true)->where('status', 'published')->get();
        $categories = Category::where('is_active', true)->get();
        $pages      = Page::where('is_active', true)->get();

        $content = view('sitemap', compact('products', 'categories', 'pages'));
        return response($content, 200)->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        $sitemap = url('/sitemap.xml');

        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin\n";
        $content .= "Disallow: /shop/cart\n";
        $content .= "Disallow: /shop/checkout\n";
        $content .= "Disallow: /shop/my-account\n\n";
        $content .= "Sitemap: {$sitemap}\n";

        return response($content, 200)->header('Content-Type', 'text/plain');
    }
}