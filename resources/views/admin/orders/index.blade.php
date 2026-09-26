@extends('layouts.admin')

@section('title', 'Orders')

@section('content')

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filters --}}
    <form method="GET" class="bg-white p-4 rounded-xl shadow mb-4 flex flex-wrap gap-3 items-center">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search order # or customer..."
               class="border rounded px-3 py-2 w-64">

        <select name="status" class="border rounded px-3 py-2">
            <option value="">All Status</option>
            @foreach ($statuses as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                    {{ ucfirst($s) }}
                </option>
            @endforeach
        </select>

        <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Filter</button>
        <a href="{{ route('admin.orders.index') }}" class="text-gray-600 hover:underline">Reset</a>
    </form>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3">Order #</th>
                    <th class="p-3">Customer</th>
                    <th class="p-3">Total</th>
                    <th class="p-3">Payment</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Date</th>
                    <th class="p-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr class="border-t">
                        <td class="p-3 font-medium">{{ $order->order_number }}</td>
                        <td class="p-3">{{ $order->user->name ?? $order->address->name ?? 'Guest' }}</td>
                        <td class="p-3 font-semibold text-indigo-600">Rs. {{ number_format($order->total) }}</td>
                        <td class="p-3">
                            <span class="text-xs px-2 py-1 rounded
                                @if($order->payment_status === 'paid') bg-green-100 text-green-800
                                @elseif($order->payment_status === 'refunded') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                            <div class="text-xs text-gray-500 mt-1">{{ strtoupper($order->payment_method) }}</div>
                        </td>
                        <td class="p-3">
                            <span class="text-xs px-2 py-1 rounded
                                @if($order->status === 'delivered') bg-green-100 text-green-800
                                @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                @elseif($order->status === 'shipped') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="p-3 text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="p-3 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}"
                               class="text-indigo-600 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-4 text-center text-gray-500">No orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>

@endsection