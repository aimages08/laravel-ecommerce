@extends('layouts.admin')

@section('title', 'Customer: ' . $customer->name)

@section('content')
    <a href="{{ route('admin.customers.index') }}" class="text-indigo-600 hover:underline mb-4 inline-block">
        ← Back to Customers
    </a>

    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold mb-1">{{ $customer->name }}</h1>
                <p class="text-sm text-gray-500">{{ $customer->email }}</p>
                @if ($customer->phone)
                    <p class="text-sm text-gray-500">{{ $customer->phone }}</p>
                @endif
                <p class="text-xs text-gray-400 mt-2">
                    Joined {{ $customer->created_at->format('M d, Y') }}
                </p>
            </div>
            <div class="text-right">
                @if ($customer->is_blocked)
                    <span class="text-xs px-3 py-1 rounded bg-red-100 text-red-700">Blocked</span>
                @else
                    <span class="text-xs px-3 py-1 rounded bg-green-100 text-green-700">Active</span>
                @endif

                <form action="{{ route('admin.customers.block', $customer) }}" method="POST" class="mt-2">
                    @csrf @method('PATCH')
                    <button class="text-sm {{ $customer->is_blocked ? 'text-green-600' : 'text-red-600' }} hover:underline">
                        {{ $customer->is_blocked ? 'Unblock Customer' : 'Block Customer' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <h2 class="font-bold text-lg mb-3">Orders ({{ $orders->count() }})</h2>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3">Order #</th>
                    <th class="p-3">Total</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Date</th>
                    <th class="p-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr class="border-t">
                        <td class="p-3 font-medium">{{ $order->order_number }}</td>
                        <td class="p-3 font-semibold text-indigo-600">Rs. {{ number_format($order->total) }}</td>
                        <td class="p-3">
                            <span class="text-xs px-2 py-1 rounded
                                @if($order->status === 'delivered') bg-green-100 text-green-700
                                @elseif($order->status === 'cancelled') bg-red-100 text-red-700
                                @else bg-yellow-100 text-yellow-800 @endif">
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
                    <tr><td colspan="5" class="p-4 text-center text-gray-500">No orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection