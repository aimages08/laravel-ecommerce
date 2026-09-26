@extends('layouts.admin')
@section('title', 'Products Report')
@section('content')

    @include('admin.reports._filter')

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3">Product</th>
                    <th class="p-3">Qty Sold</th>
                    <th class="p-3">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $r)
                    <tr class="border-t">
                        <td class="p-3">{{ $r->product_name }}</td>
                        <td class="p-3">{{ $r->qty }}</td>
                        <td class="p-3 font-semibold text-indigo-600">Rs. {{ number_format($r->revenue) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="p-4 text-center text-gray-500">No sales in this range.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $rows->links() }}</div>
@endsection