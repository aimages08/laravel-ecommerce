<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Banner;

class HomeController extends Controller
{
    public function index()
    {
        $heroBanner  = Banner::active()->where('type', 'hero')->orderBy('sort_order')->first();
        $promoBanner = Banner::active()->where('type', 'promo')->orderBy('sort_order')->first();
        $saleBanners = Banner::active()->where('type', 'sale')->orderBy('sort_order')->take(3)->get();

        $featured = Product::where('is_active', true)
            ->where('status', 'published')
            ->where('is_featured', true)
            ->with('primaryImage')
            ->take(8)->get();

        $newArrivals = Product::where('is_active', true)
            ->where('status', 'published')
            ->where('is_new', true)
            ->with('primaryImage')
            ->latest()->take(4)->get();

        $bestSellers = Product::where('is_active', true)
            ->where('status', 'published')
            ->with('primaryImage')
            ->orderByDesc('stock')
            ->take(4)->get();

        $categories = Category::where('is_active', true)
            ->withCount('products')
            ->take(6)->get();

        $brands = Brand::where('is_active', true)->take(6)->get();

        return view('shop.home', compact(
            'featured', 'newArrivals', 'bestSellers', 'categories', 'brands',
            'heroBanner', 'promoBanner', 'saleBanners'
        ));
    }
}