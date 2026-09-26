@extends('layouts.admin')

@section('title', 'Products')

@section('content')

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">All Products</h2>
        <a href="{{ route('admin.products.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">+ Add Product</a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3 w-20">Image</th>
                    <th class="p-3">Name</th>
                    <th class="p-3">Category</th>
                    <th class="p-3">Brand</th>
                    <th class="p-3">Price</th>
                    <th class="p-3">Stock</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    @php
                        $img = $product->primaryImage
                            ? (str_starts_with($product->primaryImage->path, 'http')
                                ? $product->primaryImage->path
                                : asset('storage/' . $product->primaryImage->path))
                            : null;
                    @endphp
                    <tr class="border-t">
                        <td class="p-3">
                            @if ($img)
                                <img src="{{ $img }}" class="h-10 w-10 object-cover rounded">
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="p-3 font-medium">{{ $product->name }}</td>
                        <td class="p-3 text-gray-500">{{ $product->category->name ?? '—' }}</td>
                        <td class="p-3 text-gray-500">{{ $product->brand->name ?? '—' }}</td>
                        <td class="p-3">
                            @if ($product->sale_price)
                                <span class="line-through text-gray-400 text-xs">
                                    Rs. {{ number_format($product->price) }}
                                </span>
                                Rs. {{ number_format($product->sale_price) }}
                            @else
                                Rs. {{ number_format($product->price) }}
                            @endif
                        </td>
                        <td class="p-3">
                            <span class="{{ $product->stock <= 0 ? 'text-red-600' : ($product->stock <= $product->low_stock_threshold ? 'text-yellow-600' : '') }}">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td class="p-3">
                            @if ($product->is_active)
                                <span class="text-green-600">Active</span>
                            @else
                                <span class="text-red-600">Inactive</span>
                            @endif
                        </td>
                        <td class="p-3 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('admin.products.edit', $product) }}"
                               class="text-indigo-600 hover:underline">Edit</a>
                            <form action="{{ route('admin.products.destroy', $product) }}"
                                  method="POST" class="inline"
                                  onsubmit="return confirm('Delete this product?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="p-4 text-center text-gray-500">No products yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $products->links() }}</div>
@endsection