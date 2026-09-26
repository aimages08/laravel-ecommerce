@extends('layouts.admin')

@section('title', 'Add Banner')

@section('content')
    <div class="bg-white p-6 rounded-xl shadow max-w-3xl">
        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block mb-1 font-medium">Type *</label>
                    <select name="type" class="w-full border rounded px-3 py-2" required>
                        <option value="hero" {{ old('type') === 'hero' ? 'selected' : '' }}>Hero (top slider)</option>
                        <option value="promo" {{ old('type') === 'promo' ? 'selected' : '' }}>Promo (50% off)</option>
                        <option value="sale" {{ old('type') === 'sale' ? 'selected' : '' }}>Sale</option>
                    </select>
                    @error('type')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block mb-1 font-medium">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                           class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Title *</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="w-full border rounded px-3 py-2">
                @error('title')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Subtitle</label>
                <input type="text" name="subtitle" value="{{ old('subtitle') }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Description</label>
                <textarea name="description" rows="3"
                          class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block mb-1 font-medium">Button Text</label>
                    <input type="text" name="button_text" value="{{ old('button_text') }}"
                           placeholder="Shop Now"
                           class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block mb-1 font-medium">Button URL</label>
                    <input type="text" name="button_url" value="{{ old('button_url') }}"
                           placeholder="/shop/products"
                           class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Background Color (optional)</label>
                <input type="text" name="bg_color" value="{{ old('bg_color') }}"
                       placeholder="from-indigo-600 to-purple-600"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Banner Image *</label>
                <input type="file" name="image" accept="image/*" required
                       class="w-full border rounded px-3 py-2">
                @error('image')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block mb-1 font-medium">Starts At</label>
                    <input type="date" name="starts_at" value="{{ old('starts_at') }}"
                           class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block mb-1 font-medium">Ends At</label>
                    <input type="date" name="ends_at" value="{{ old('ends_at') }}"
                           class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1" checked class="mr-2"> Active
                </label>
            </div>

            <div class="flex gap-2">
                <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Save</button>
                <a href="{{ route('admin.banners.index') }}" class="px-4 py-2 rounded border">Cancel</a>
            </div>
        </form>
    </div>
@endsection