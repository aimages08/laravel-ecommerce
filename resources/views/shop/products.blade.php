@extends('layouts.shop')

@section('title', 'Products')

@section('content')

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">All Products</h1>
        <span class="text-sm text-gray-500">{{ $products->total() }} items</span>
    </div>

    <div class="grid md:grid-cols-4 gap-6">

        {{-- Sidebar Filters --}}
        <aside class="md:col-span-1">
            <form method="GET" class="bg-white rounded-xl shadow p-5 space-y-4 sticky top-4">

                <div>
                    <label class="block text-sm font-medium mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Product name..."
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Category</label>
                    <select name="category" class="w-full border rounded px-3 py-2 text-sm">
                        <option value="">All Categories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ request('category') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Brands</label>
                    <div class="max-h-40 overflow-y-auto space-y-1">
                        @foreach ($brands as $brand)
                            <label class="flex items-center text-sm">
                                <input type="checkbox" name="brand[]" value="{{ $brand->id }}"
                                       {{ in_array($brand->id, (array) request('brand', [])) ? 'checked' : '' }}
                                       class="mr-2">
                                {{ $brand->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Price Range</label>
                    <div class="flex gap-2">
                        <input type="number" name="min_price" value="{{ request('min_price') }}"
                               placeholder="Min" class="w-full border rounded px-2 py-1 text-sm">
                        <input type="number" name="max_price" value="{{ request('max_price') }}"
                               placeholder="Max" class="w-full border rounded px-2 py-1 text-sm">
                    </div>
                </div>

                <div class="flex gap-2 pt-2">
                    <button class="flex-1 bg-indigo-600 text-white py-2 rounded text-sm hover:bg-indigo-700">
                        Apply
                    </button>
                    <a href="{{ route('shop.products') }}"
                       class="flex-1 text-center py-2 rounded border text-sm">Reset</a>
                </div>
            </form>
        </aside>

        {{-- Products --}}
        <div class="md:col-span-3">

            {{-- Sort bar --}}
            <div class="bg-white rounded-xl shadow p-3 mb-4 flex items-center justify-between flex-wrap gap-2">
                <p class="text-sm text-gray-500">
                    Showing {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} of {{ $products->total() }}
                </p>
                <form method="GET" class="flex items-center gap-2">
                    @foreach (request()->except('sort', 'page') as $key => $value)
                        @if (is_array($value))
                            @foreach ($value as $v)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <label class="text-sm">Sort:</label>
                    <select name="sort" onchange="this.form.submit()"
                            class="border rounded px-3 py-1 text-sm">
                        <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low → High</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High → Low</option>
                        <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name: A → Z</option>
                    </select>
                </form>
            </div>

            {{-- Product Grid (using shared partial) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($products as $product)
                    @include('shop.partials.product-card', ['product' => $product])
                @empty
                    <div class="col-span-full bg-white rounded-xl shadow p-12 text-center">
                        <p class="text-gray-500 mb-3">No products found.</p>
                        <a href="{{ route('shop.products') }}" class="text-indigo-600 hover:underline">
                            Clear filters
                        </a>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">{{ $products->links() }}</div>
        </div>
    </div>
@endsection