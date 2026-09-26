@extends('layouts.admin')
@section('title', 'Add Payment Method')
@section('content')

    <div class="bg-white p-6 rounded-xl shadow max-w-2xl">
        <form action="{{ route('admin.payment-methods.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block mb-1 font-medium">Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="e.g. Stripe, JazzCash, PayPal"
                       class="w-full border rounded px-3 py-2">
                @error('name')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Code *</label>
                <input type="text" name="code" value="{{ old('code') }}" required
                       placeholder="lowercase, no spaces (e.g. stripe)"
                       class="w-full border rounded px-3 py-2">
                <p class="text-xs text-gray-500 mt-1">Unique identifier used in code.</p>
                @error('code')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4 grid grid-cols-2 gap-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_enabled" value="1"
                           {{ old('is_enabled') ? 'checked' : '' }} class="mr-2">
                    Enabled
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_test_mode" value="1"
                           {{ old('is_test_mode', true) ? 'checked' : '' }} class="mr-2">
                    Test Mode
                </label>
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Instructions (shown to customer)</label>
                <textarea name="instructions" rows="3"
                          class="w-full border rounded px-3 py-2">{{ old('instructions') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                       class="w-32 border rounded px-3 py-2">
            </div>

            <div class="flex gap-2">
                <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Create</button>
                <a href="{{ route('admin.payment-methods.index') }}" class="px-4 py-2 rounded border">Cancel</a>
            </div>
        </form>
    </div>
@endsection