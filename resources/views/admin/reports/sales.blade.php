@extends('layouts.admin')
@section('title', 'Sales Report')
@section('content')

    @include('admin.reports._filter')

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl shadow">
            <p class="text-xs text-gray-500">Total Orders</p>
            <p class="text-xl font-bold mt-1">{{ $totals['orders'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow">
            <p class="text-xs text-gray-500">Revenue</p>
            <p class="text-xl font-bold mt-1">Rs. {{ number_format($totals['revenue']) }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow">
            <p class="text-xs text-gray-500">Cancelled</p>
            <p class="text-xl font-bold mt-1 text-red-600">{{ $totals['cancelled'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow">
            <p class="text-xs text-gray-500">Avg Order</p>
            <p class="text-xl font-bold mt-1">Rs. {{ number_format($totals['avg']) }}</p>
        </div>
    </div>

    <div class="flex justify-end mb-3">
        <a href="{{ route('admin.reports.sales.csv', request()->query()) }}"
           class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">
            📥 Export CSV
        </a>
    </div>

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
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $o)
                    <tr class="border-t">
                        <td class="p-3 font-medium">{{ $o->order_number }}</td>
                        <td class="p-3">{{ $o->user->name ?? 'Guest' }}</td>
                        <td class="p-3 font-semibold text-indigo-600">Rs. {{ number_format($o->total) }}</td>
                        <td class="p-3">{{ strtoupper($o->payment_method) }}</td>
                        <td class="p-3">{{ ucfirst($o->status) }}</td>
                        <td class="p-3 text-gray-500">{{ $o->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-4 text-center text-gray-500">No orders in this range.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
@endsection