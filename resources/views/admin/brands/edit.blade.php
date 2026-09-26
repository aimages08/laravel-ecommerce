@extends('layouts.admin')

@section('title', 'Edit Brand')

@section('content')
    <div class="bg-white p-6 rounded-xl shadow max-w-2xl">
        <form action="{{ route('admin.brands.update', $brand) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block mb-1 font-medium">Name</label>
                <input type="text" name="name" value="{{ old('name', $brand->name) }}"
                       class="w-full border rounded px-3 py-2">
                @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Description</label>
                <textarea name="description" rows="3"
                          class="w-full border rounded px-3 py-2">{{ old('description', $brand->description) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1"
                           {{ $brand->is_active ? 'checked' : '' }} class="mr-2"> Active
                </label>
            </div>

            <div class="flex gap-2">
                <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Update</button>
                <a href="{{ route('admin.brands.index') }}" class="px-4 py-2 rounded border">Cancel</a>
            </div>
        </form>
    </div>
@endsection