@extends('layouts.admin')
@section('title', 'Inventory')
@section('content')

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl shadow">
            <p class="text-xs text-gray-500">Total Products</p>
            <p class="text-xl font-bold mt-1">{{ $totals['all'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow">
            <p class="text-xs text-gray-500">Low Stock</p>
            <p class="text-xl font-bold mt-1 text-yellow-600">{{ $totals['low'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow">
            <p class="text-xs text-gray-500">Out of Stock</p>
            <p class="text-xl font-bold mt-1 text-red-600">{{ $totals['out'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow">
            <p class="text-xs text-gray-500">Inventory Value</p>
            <p class="text-xl font-bold mt-1">Rs. {{ number_format($totals['value']) }}</p>
        </div>
    </div>

    <div class="bg-white p-4 rounded-xl shadow mb-4 flex flex-wrap gap-3 items-center justify-between">
        <form method="GET" class="flex gap-3 items-center flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search product..."
                   class="border rounded px-3 py-2 text-sm w-56">
            <select name="filter" class="border rounded px-3 py-2 text-sm" onchange="this.form.submit()">
                <option value="">All Stock</option>
                <option value="low" {{ request('filter') === 'low' ? 'selected' : '' }}>Low Stock</option>
                <option value="out" {{ request('filter') === 'out' ? 'selected' : '' }}>Out of Stock</option>
            </select>
            <button class="bg-indigo-600 text-white px-4 py-2 rounded text-sm">Filter</button>
            <a href="{{ route('admin.inventory.index') }}" class="text-gray-500 text-sm hover:underline">Reset</a>
        </form>

        <a href="{{ route('admin.inventory.history') }}"
           class="text-indigo-600 text-sm hover:underline">📜 View Full History</a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3">Product</th>
                    <th class="p-3">Category</th>
                    <th class="p-3">Stock</th>
                    <th class="p-3">Threshold</th>
                    <th class="p-3">Price</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $p)
                    <tr class="border-t">
                        <td class="p-3 font-medium">{{ $p->name }}</td>
                        <td class="p-3 text-gray-500">{{ $p->category->name ?? '—' }}</td>
                        <td class="p-3">
                            @if ($p->stock <= 0)
                                <span class="text-xs px-2 py-1 rounded bg-red-100 text-red-700">Out ({{ $p->stock }})</span>
                            @elseif ($p->stock <= $p->low_stock_threshold)
                                <span class="text-xs px-2 py-1 rounded bg-yellow-100 text-yellow-800">Low ({{ $p->stock }})</span>
                            @else
                                <span class="text-xs px-2 py-1 rounded bg-green-100 text-green-700">{{ $p->stock }}</span>
                            @endif
                        </td>
                        <td class="p-3 text-gray-500">{{ $p->low_stock_threshold }}</td>
                        <td class="p-3">Rs. {{ number_format($p->sale_price ?? $p->price) }}</td>
                        <td class="p-3 text-right">
                            <a href="{{ route('admin.inventory.adjust-form', $p) }}"
                               class="text-indigo-600 hover:underline">Adjust Stock</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-4 text-center text-gray-500">No products found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $products->links() }}</div>
@endsection