@extends('layouts.admin')
@section('title', 'Add Page')
@section('content')
    <div class="bg-white p-6 rounded-xl shadow max-w-3xl">
        <form action="{{ route('admin.pages.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block mb-1 font-medium">Title *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block mb-1 font-medium">Slug (leave blank to auto)</label>
                    <input type="text" name="slug" value="{{ old('slug') }}"
                           class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Content (HTML allowed)</label>
                <textarea name="content" rows="12" class="w-full border rounded px-3 py-2 font-mono text-sm">{{ old('content') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block mb-1 font-medium">Meta Title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title') }}"
                           class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block mb-1 font-medium">Meta Description</label>
                    <input type="text" name="meta_description" value="{{ old('meta_description') }}"
                           class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                       class="w-32 border rounded px-3 py-2">
            </div>

            <div class="mb-4 space-x-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="show_in_footer" value="1" checked class="mr-2"> Show in Footer
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1" checked class="mr-2"> Active
                </label>
            </div>

            <div class="flex gap-2">
                <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Save</button>
                <a href="{{ route('admin.pages.index') }}" class="px-4 py-2 rounded border">Cancel</a>
            </div>
        </form>
    </div>
@endsection