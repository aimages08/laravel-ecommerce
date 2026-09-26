@extends('layouts.admin')
@section('title', 'Add Coupon')

@section('content')
    <div class="bg-white p-6 rounded-xl shadow max-w-2xl">
        <form action="{{ route('admin.coupons.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block mb-1 font-medium">Code *</label>
                    <input type="text" name="code" value="{{ old('code') }}"
                           class="w-full border rounded px-3 py-2 uppercase" placeholder="WELCOME10">
                    @error('code')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block mb-1 font-medium">Type *</label>
                    <select name="type" class="w-full border rounded px-3 py-2">
                        <option value="percent" {{ old('type') === 'percent' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Fixed (Rs.)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block mb-1 font-medium">Value *</label>
                    <input type="number" step="0.01" name="value" value="{{ old('value') }}"
                           class="w-full border rounded px-3 py-2">
                    @error('value')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block mb-1 font-medium">Min Order (Rs.)</label>
                    <input type="number" step="0.01" name="min_order" value="{{ old('min_order', 0) }}"
                           class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block mb-1 font-medium">Max Discount (Rs.)</label>
                    <input type="number" step="0.01" name="max_discount" value="{{ old('max_discount') }}"
                           class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block mb-1 font-medium">Usage Limit</label>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit') }}"
                           class="w-full border rounded px-3 py-2" placeholder="Empty = unlimited">
                </div>
                <div>
                    <label class="block mb-1 font-medium">Starts At</label>
                    <input type="date" name="starts_at" value="{{ old('starts_at') }}"
                           class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block mb-1 font-medium">Ends At</label>
                    <input type="date" name="ends_at" value="{{ old('ends_at') }}"
                           class="w-full border rounded px-3 py-2">
                    @error('ends_at')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1" checked class="mr-2"> Active
                </label>
            </div>

            <div class="flex gap-2">
                <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Save</button>
                <a href="{{ route('admin.coupons.index') }}" class="px-4 py-2 rounded border">Cancel</a>
            </div>
        </form>
    </div>
@endsection