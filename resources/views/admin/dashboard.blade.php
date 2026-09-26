@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6">
        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-500 text-sm">Total Sales</p>
            <p class="text-2xl font-bold mt-1">Rs. {{ number_format($totalSales) }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-500 text-sm">Today's Sales</p>
            <p class="text-2xl font-bold mt-1">Rs. {{ number_format($todaySales) }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-500 text-sm">This Month</p>
            <p class="text-2xl font-bold mt-1">Rs. {{ number_format($monthSales) }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-500 text-sm">Total Orders</p>
            <p class="text-2xl font-bold mt-1">{{ $totalOrders }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-yellow-50 p-5 rounded-xl shadow border-l-4 border-yellow-400">
            <p class="text-gray-600 text-sm">Pending Orders</p>
            <p class="text-2xl font-bold mt-1">{{ $pendingOrders }}</p>
        </div>
        <div class="bg-green-50 p-5 rounded-xl shadow border-l-4 border-green-400">
            <p class="text-gray-600 text-sm">Completed</p>
            <p class="text-2xl font-bold mt-1">{{ $completedOrders }}</p>
        </div>
        <div class="bg-red-50 p-5 rounded-xl shadow border-l-4 border-red-400">
            <p class="text-gray-600 text-sm">Cancelled</p>
            <p class="text-2xl font-bold mt-1">{{ $cancelledOrders }}</p>
        </div>
        <div class="bg-indigo-50 p-5 rounded-xl shadow border-l-4 border-indigo-400">
            <p class="text-gray-600 text-sm">Customers</p>
            <p class="text-2xl font-bold mt-1">{{ $totalCustomers }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-500 text-sm">Products</p>
            <p class="text-2xl font-bold mt-1">{{ $totalProducts }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-500 text-sm">Low Stock</p>
            <p class="text-2xl font-bold mt-1 text-yellow-600">{{ $lowStock }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-500 text-sm">Out of Stock</p>
            <p class="text-2xl font-bold mt-1 text-red-600">{{ $outOfStock }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Quick Actions</p>
                <a href="{{ route('admin.products.create') }}" class="text-indigo-600 text-sm hover:underline">
                    + Add Product
                </a>
            </div>
        </div>
    </div>

    {{-- Low Stock Warning Table --}}
    @if ($lowStockProducts->count())
        <div class="bg-white rounded-xl shadow mb-6 border-l-4 border-yellow-400">
            <div class="p-5 border-b">
                <h3 class="font-bold text-lg">⚠️ Low Stock Warning</h3>
                <p class="text-sm text-gray-500">The following products need restocking soon.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="p-3">Product</th>
                            <th class="p-3">Current Stock</th>
                            <th class="p-3">Threshold</th>
                            <th class="p-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lowStockProducts as $p)
                            <tr class="border-t">
                                <td class="p-3 font-medium">{{ $p->name }}</td>
                                <td class="p-3 text-yellow-700 font-semibold">{{ $p->stock }}</td>
                                <td class="p-3 text-gray-500">{{ $p->low_stock_threshold }}</td>
                                <td class="p-3 text-right">
                                    <a href="{{ route('admin.inventory.adjust-form', $p) }}"
                                       class="text-indigo-600 hover:underline">Restock</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Out of Stock Alert --}}
    @if ($outOfStock > 0)
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <p class="text-red-700 font-semibold">
                🚨 {{ $outOfStock }} product(s) are out of stock!
            </p>
            <a href="{{ route('admin.inventory.index', ['filter' => 'out']) }}"
               class="text-red-600 text-sm hover:underline">View them →</a>
        </div>
    @endif

    {{-- Recent Orders + Customers --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <div class="bg-white rounded-xl shadow p-5">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold">Recent Orders</h3>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-indigo-600 hover:underline">View all</a>
            </div>

            @forelse ($recentOrders as $order)
                <div class="flex justify-between py-2 border-b last:border-0 text-sm">
                    <div>
                        <a href="{{ route('admin.orders.show', $order) }}"
                           class="font-medium text-indigo-600 hover:underline">
                            {{ $order->order_number }}
                        </a>
                        <p class="text-gray-500 text-xs">{{ $order->user->name ?? 'Guest' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold">Rs. {{ number_format($order->total) }}</p>
                        <p class="text-xs text-gray-500">{{ ucfirst($order->status) }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">No orders yet.</p>
            @endforelse
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-bold mb-3">Recent Customers</h3>

            @forelse ($recentCustomers as $customer)
                <div class="flex justify-between py-2 border-b last:border-0 text-sm">
                    <div>
                        <p class="font-medium">{{ $customer->name }}</p>
                        <p class="text-gray-500 text-xs">{{ $customer->email }}</p>
                    </div>
                    <p class="text-xs text-gray-400">{{ $customer->created_at->diffForHumans() }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-500">No customers yet.</p>
            @endforelse
        </div>

    </div>

@endsection