@extends('layouts.admin')
@section('title', 'Payments Report')
@section('content')

    @include('admin.reports._filter')

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3">Payment Method</th>
                    <th class="p-3">Orders</th>
                    <th class="p-3">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $r)
                    <tr class="border-t">
                        <td class="p-3 font-medium">{{ strtoupper($r->payment_method) }}</td>
                        <td class="p-3">{{ $r->count }}</td>
                        <td class="p-3 font-semibold text-indigo-600">Rs. {{ number_format($r->revenue) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="p-4 text-center text-gray-500">No payments.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection