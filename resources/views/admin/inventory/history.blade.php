@extends('layouts.admin')
@section('title', 'Inventory History')
@section('content')

    <div class="mb-4">
        <a href="{{ route('admin.inventory.index') }}" class="text-indigo-600 hover:underline">
            ← Back to Inventory
        </a>
    </div>

    <form method="GET" class="bg-white p-4 rounded-xl shadow mb-4 flex flex-wrap gap-3 items-center">
        <input type="text" name="product_id" value="{{ request('product_id') }}"
               placeholder="Product ID"
               class="border rounded px-3 py-2 text-sm w-32">
        <select name="type" class="border rounded px-3 py-2 text-sm">
            <option value="">All Types</option>
            @foreach (['in','out','adjustment','sale','return'] as $t)
                <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
            @endforeach
        </select>
        <button class="bg-indigo-600 text-white px-4 py-2 rounded text-sm">Filter</button>
        <a href="{{ route('admin.inventory.history') }}" class="text-gray-500 text-sm hover:underline">Reset</a>
    </form>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3">Date</th>
                    <th class="p-3">Product</th>
                    <th class="p-3">Type</th>
                    <th class="p-3">Change</th>
                    <th class="p-3">Before → After</th>
                    <th class="p-3">By</th>
                    <th class="p-3">Note</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $t)
                    <tr class="border-t">
                        <td class="p-3 text-gray-500">{{ $t->created_at->format('M d, Y H:i') }}</td>
                        <td class="p-3">{{ $t->product->name ?? '(deleted)' }}</td>
                        <td class="p-3">{{ ucfirst($t->type) }}</td>
                        <td class="p-3 {{ $t->quantity > 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">
                            {{ $t->quantity > 0 ? '+' : '' }}{{ $t->quantity }}
                        </td>
                        <td class="p-3">{{ $t->before_qty }} → {{ $t->after_qty }}</td>
                        <td class="p-3 text-gray-500">{{ $t->user->name ?? '—' }}</td>
                        <td class="p-3 text-gray-500 text-xs">{{ $t->note ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-4 text-center text-gray-500">No transactions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $transactions->links() }}</div>
@endsection