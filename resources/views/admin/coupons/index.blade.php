@extends('layouts.admin')
@section('title', 'Coupons')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">All Coupons</h2>
        <a href="{{ route('admin.coupons.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">+ Add Coupon</a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3">Code</th>
                    <th class="p-3">Discount</th>
                    <th class="p-3">Min Order</th>
                    <th class="p-3">Used</th>
                    <th class="p-3">Valid Until</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($coupons as $c)
                    <tr class="border-t">
                        <td class="p-3 font-mono font-bold">{{ $c->code }}</td>
                        <td class="p-3">
                            {{ $c->type === 'percent' ? $c->value . '%' : 'Rs. ' . number_format($c->value) }}
                            @if ($c->max_discount) <span class="text-xs text-gray-500">(max Rs. {{ number_format($c->max_discount) }})</span> @endif
                        </td>
                        <td class="p-3">Rs. {{ number_format($c->min_order) }}</td>
                        <td class="p-3">{{ $c->used_count }} / {{ $c->usage_limit ?? '∞' }}</td>
                        <td class="p-3 text-gray-500">{{ $c->ends_at?->format('M d, Y') ?? '—' }}</td>
                        <td class="p-3">
                            @if ($c->is_active)
                                <span class="text-green-600">Active</span>
                            @else
                                <span class="text-red-600">Inactive</span>
                            @endif
                        </td>
                        <td class="p-3 text-right space-x-2">
                            <a href="{{ route('admin.coupons.edit', $c) }}" class="text-indigo-600 hover:underline">Edit</a>
                            <form action="{{ route('admin.coupons.destroy', $c) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Delete coupon?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-4 text-center text-gray-500">No coupons yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $coupons->links() }}</div>
@endsection