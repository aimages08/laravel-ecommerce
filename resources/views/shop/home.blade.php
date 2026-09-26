@extends('layouts.shop')

@section('title', 'Home')

@section('content')

    {{-- ==================== HERO BANNER ==================== --}}
    @if ($heroBanner)
        <div class="relative rounded-2xl overflow-hidden mb-12 {{ $heroBanner->bg_color ? '' : 'bg-gradient-to-br from-indigo-600 via-indigo-500 to-purple-600' }}"
            @if($heroBanner->bg_color) style="background: {{ $heroBanner->bg_color }}" @endif>
            <div class="grid grid-cols-1 md:grid-cols-2 items-center gap-4">
                <div class="p-8 md:p-14">
                    @if ($heroBanner->subtitle)
                        <p class="text-indigo-100 mb-2">{{ $heroBanner->subtitle }}</p>
                    @endif
                    <h1 class="text-3xl md:text-5xl font-bold text-white leading-tight mb-4">
                        {{ $heroBanner->title }}
                    </h1>
                    @if ($heroBanner->description)
                        <p class="text-indigo-100 mb-6">{{ $heroBanner->description }}</p>
                    @endif
                    @if ($heroBanner->button_text)
                        <a href="{{ $heroBanner->button_url ?? '#' }}"
                        class="inline-block bg-white text-indigo-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100">
                            {{ $heroBanner->button_text }} →
                        </a>
                    @endif
                </div>
                @if ($heroBanner->image_url)
                    <div class="flex items-center justify-center p-8">
                        <img src="{{ $heroBanner->image_url }}" class="rounded-2xl shadow-lg w-full max-w-md">
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ==================== CATEGORIES ==================== --}}
    @if ($categories->count())
        <section class="mb-14">
            <div class="text-center mb-8">
                <p class="text-gray-500 text-sm">Popular in market</p>
                <h2 class="text-3xl font-bold">Shop by Category</h2>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach ($categories as $cat)
                    <a href="{{ route('shop.products', ['category' => $cat->id]) }}"
                       class="bg-white rounded-xl shadow hover:shadow-lg transition p-5 text-center group">
                        <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-2xl group-hover:bg-indigo-600 group-hover:text-white transition">
                            🛍️
                        </div>
                        <p class="font-semibold text-sm">{{ $cat->name }}</p>
                        <p class="text-xs text-gray-500">{{ $cat->products_count }} items</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ==================== FEATURED PRODUCTS ==================== --}}
    @if ($featured->count())
        <section class="mb-14">
            <div class="flex justify-between items-end mb-6">
                <div>
                    <p class="text-gray-500 text-sm">Handpicked for you</p>
                    <h2 class="text-3xl font-bold">Featured Products</h2>
                </div>
                <a href="{{ route('shop.products') }}" class="text-indigo-600 text-sm hover:underline">View all →</a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ($featured as $product)
                    @include('shop.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </section>
    @endif

    {{-- ==================== 50% OFF PROMO BANNER ==================== --}}
@if ($promoBanner)
    <section class="mb-14">
        <div class="bg-gradient-to-r from-sky-100 to-sky-200 rounded-2xl p-6 md:p-14">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                <div>
                    @if ($promoBanner->subtitle)
                        <p class="text-sky-700 font-semibold mb-2">{{ $promoBanner->subtitle }}</p>
                    @endif
                    <h2 class="text-2xl md:text-5xl font-bold mb-3">{{ $promoBanner->title }}</h2>
                    @if ($promoBanner->description)
                        <p class="text-gray-600 mb-6">{{ $promoBanner->description }}</p>
                    @endif
                    @if ($promoBanner->button_text)
                        <a href="{{ $promoBanner->button_url ?? '#' }}"
                           class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700">
                            {{ $promoBanner->button_text }} →
                        </a>
                    @endif
                </div>
                @if ($promoBanner->image_url)
                    <div class="flex justify-center">
                        <img src="{{ $promoBanner->image_url }}"
                             class="rounded-xl shadow-lg w-full max-w-xs md:max-w-sm">
                    </div>
                @endif
            </div>
        </div>
    </section>
@endif

    {{-- ==================== NEW ARRIVALS ==================== --}}
    @if ($newArrivals->count())
        <section class="mb-14">
            <div class="flex justify-between items-end mb-6">
                <div>
                    <p class="text-gray-500 text-sm">Just landed</p>
                    <h2 class="text-3xl font-bold">New Arrivals</h2>
                </div>
                <a href="{{ route('shop.products', ['sort' => 'newest']) }}"
                   class="text-indigo-600 text-sm hover:underline">View all →</a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ($newArrivals as $product)
                    @include('shop.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </section>
    @endif


    {{-- ==================== SALE BANNERS (3 cols) ==================== --}}
        @if ($saleBanners->count())
            <section class="mb-14">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach ($saleBanners as $sale)
                        <div class="relative rounded-2xl overflow-hidden bg-gray-100 h-56 group">

                            @if ($sale->image_url)
                                <img src="{{ $sale->image_url }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @endif

                            {{-- Overlay --}}
                            <div class="absolute inset-0 bg-black/30"></div>

                            <div class="absolute bottom-0 left-0 p-6 text-white">
                                @if ($sale->subtitle)
                                    <p class="text-sm opacity-90 mb-1">{{ $sale->subtitle }}</p>
                                @endif
                                @if ($sale->title)
                                    <h3 class="text-2xl font-bold mb-2">{{ $sale->title }}</h3>
                                @endif
                                @if ($sale->button_text)
                                    <a href="{{ $sale->button_url ?? '#' }}"
                                    class="inline-block bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">
                                        {{ $sale->button_text }} →
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

    {{-- ==================== BEST SELLERS ==================== --}}
    @if ($bestSellers->count())
        <section class="mb-14">
            <div class="flex justify-between items-end mb-6">
                <div>
                    <p class="text-gray-500 text-sm">Popular in market</p>
                    <h2 class="text-3xl font-bold">Best Sellers</h2>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ($bestSellers as $product)
                    @include('shop.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </section>
    @endif

    {{-- ==================== BRANDS ==================== --}}
    @if ($brands->count())
        <section class="mb-14">
            <div class="text-center mb-8">
                <p class="text-gray-500 text-sm">Trusted names</p>
                <h2 class="text-3xl font-bold">Top Brands</h2>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach ($brands as $brand)
                    <a href="{{ route('shop.products', ['brand' => [$brand->id]]) }}"
                       class="bg-white rounded-xl shadow hover:shadow-lg transition p-6 text-center font-semibold">
                        {{ $brand->name }}
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ==================== NEWSLETTER ==================== --}}
    <section class="bg-white rounded-2xl shadow p-10 text-center">
        <h2 class="text-2xl font-bold mb-2">Get Updates From Anywhere</h2>
        <p class="text-gray-500 mb-6">Subscribe to receive new arrivals & offers.</p>
        <form class="max-w-lg mx-auto flex gap-2">
            <input type="email" placeholder="Enter your email"
                   class="flex-1 border rounded-l-lg px-4 py-3">
            <button class="bg-indigo-600 text-white px-6 py-3 rounded-r-lg hover:bg-indigo-700 font-semibold">
                Subscribe
            </button>
        </form>
    </section>

@endsection