@extends('layouts.admin')
@section('title', 'Customers Report')
@section('content')

    @include('admin.reports._filter')

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3">Customer</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Orders</th>
                    <th class="p-3">Total Spent</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $c)
                    <tr class="border-t">
                        <td class="p-3 font-medium">{{ $c->name }}</td>
                        <td class="p-3 text-gray-500">{{ $c->email }}</td>
                        <td class="p-3">{{ $c->orders_count }}</td>
                        <td class="p-3 font-semibold text-indigo-600">
                            Rs. {{ number_format($c->total_spent ?? 0) }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-4 text-center text-gray-500">No customers.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $rows->links() }}</div>
@endsection