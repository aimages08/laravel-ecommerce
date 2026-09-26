@extends('layouts.admin')
@section('title', 'Adjust Stock — ' . $product->name)
@section('content')

    <a href="{{ route('admin.inventory.index') }}" class="text-indigo-600 hover:underline mb-4 inline-block">
        ← Back to Inventory
    </a>

    <div class="grid md:grid-cols-2 gap-6">

        {{-- Adjust Form --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-bold text-lg mb-4">{{ $product->name }}</h2>
            <p class="text-sm mb-4 text-gray-500">
                Current stock: <strong>{{ $product->stock }}</strong>
            </p>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.inventory.adjust', $product) }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block mb-1 font-medium">Type</label>
                    <select name="type" class="w-full border rounded px-3 py-2" required>
                        <option value="in">Stock In (+)</option>
                        <option value="out">Stock Out (-)</option>
                        <option value="adjustment">Set Exact Quantity</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Quantity</label>
                    <input type="number" name="quantity" required
                           class="w-full border rounded px-3 py-2">
                    <p class="text-xs text-gray-500 mt-1">
                        For "in" and "out" this is the change. For "adjustment" this sets the exact stock.
                    </p>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Note (optional)</label>
                    <textarea name="note" rows="2" class="w-full border rounded px-3 py-2"></textarea>
                </div>

                <button class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
                    Save Adjustment
                </button>
            </form>
        </div>

        {{-- Recent History --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-bold text-lg mb-4">Recent History</h2>

            @forelse ($history as $h)
                <div class="border-b py-2 text-sm">
                    <div class="flex justify-between">
                        <span class="font-medium">
                            {{ ucfirst($h->type) }}
                            <span class="{{ $h->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                ({{ $h->quantity > 0 ? '+' : '' }}{{ $h->quantity }})
                            </span>
                        </span>
                        <span class="text-xs text-gray-400">{{ $h->created_at->format('M d, H:i') }}</span>
                    </div>
                    <p class="text-xs text-gray-500">
                        {{ $h->before_qty }} → {{ $h->after_qty }}
                        @if ($h->user) | by {{ $h->user->name }} @endif
                    </p>
                    @if ($h->note)<p class="text-xs text-gray-400">{{ $h->note }}</p>@endif
                </div>
            @empty
                <p class="text-gray-500 text-sm">No history yet.</p>
            @endforelse
        </div>
    </div>
@endsection