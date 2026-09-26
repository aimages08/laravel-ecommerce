@extends('layouts.admin')

@section('title', 'Edit Banner')

@section('content')
    <div class="bg-white p-6 rounded-xl shadow max-w-3xl">
        <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block mb-1 font-medium">Type *</label>
                    <select name="type" class="w-full border rounded px-3 py-2">
                        <option value="hero" {{ old('type', $banner->type) === 'hero' ? 'selected' : '' }}>Hero (top slider)</option>
                        <option value="promo" {{ old('type', $banner->type) === 'promo' ? 'selected' : '' }}>Promo (50% off)</option>
                        <option value="sale" {{ old('type', $banner->type) === 'sale' ? 'selected' : '' }}>Sale</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-1 font-medium">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order) }}"
                           class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Title</label>
                <input type="text" name="title" value="{{ old('title', $banner->title) }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Subtitle</label>
                <input type="text" name="subtitle" value="{{ old('subtitle', $banner->subtitle) }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Description</label>
                <textarea name="description" rows="3"
                          class="w-full border rounded px-3 py-2">{{ old('description', $banner->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block mb-1 font-medium">Button Text</label>
                    <input type="text" name="button_text" value="{{ old('button_text', $banner->button_text) }}"
                           placeholder="Shop Now"
                           class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block mb-1 font-medium">Button URL</label>
                    <input type="text" name="button_url" value="{{ old('button_url', $banner->button_url) }}"
                           placeholder="/shop/products"
                           class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Background Color (optional)</label>
                <input type="text" name="bg_color" value="{{ old('bg_color', $banner->bg_color) }}"
                       placeholder="from-indigo-600 to-purple-600"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Banner Image</label>
                @if ($banner->image_url)
                    <img src="{{ $banner->image_url }}" class="h-24 rounded mb-2">
                @endif
                <input type="file" name="image" accept="image/*"
                       class="w-full border rounded px-3 py-2">
                <p class="text-xs text-gray-500 mt-1">Leave empty to keep current image.</p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block mb-1 font-medium">Starts At</label>
                    <input type="date" name="starts_at"
                           value="{{ old('starts_at', $banner->starts_at?->format('Y-m-d')) }}"
                           class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block mb-1 font-medium">Ends At</label>
                    <input type="date" name="ends_at"
                           value="{{ old('ends_at', $banner->ends_at?->format('Y-m-d')) }}"
                           class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1"
                           {{ $banner->is_active ? 'checked' : '' }} class="mr-2"> Active
                </label>
            </div>

            <div class="flex gap-2">
                <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Update</button>
                <a href="{{ route('admin.banners.index') }}" class="px-4 py-2 rounded border">Cancel</a>
            </div>
        </form>
    </div>
@endsection