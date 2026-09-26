@extends('layouts.admin')
@section('title', 'Add Attribute')
@section('content')

    <div class="bg-white p-6 rounded-xl shadow max-w-3xl" x-data="{ newRow: 0, type: 'select' }">
        <form action="{{ route('admin.attributes.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block mb-1 font-medium">Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="e.g. Size, Color, Material"
                           class="w-full border rounded px-3 py-2">
                    @error('name')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block mb-1 font-medium">Type *</label>
                    <select name="type" x-model="type" class="w-full border rounded px-3 py-2">
                        <option value="select">Select (dropdown)</option>
                        <option value="color">Color (with swatch)</option>
                        <option value="text">Text (free input)</option>
                    </select>
                </div>
            </div>

            <div class="mb-4 grid grid-cols-2 gap-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1" checked class="mr-2"> Active
                </label>
                <div>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                           placeholder="Sort order"
                           class="w-full border rounded px-3 py-2">
                </div>
            </div>

            {{-- Values --}}
            <h3 class="font-bold mt-6 mb-3 border-t pt-4">Values (optional, add now or later)</h3>

            <template x-for="i in newRow" :key="i">
                <div class="flex items-center gap-2 mb-2">
                    <input type="text" name="new_values[]"
                           placeholder="Value (e.g. S, M, Red)"
                           class="flex-1 border rounded px-2 py-1 text-sm">

                    <template x-if="type === 'color'">
                        <input type="color" name="new_colors[]" value="#000000"
                               class="border rounded h-9 w-20">
                    </template>
                </div>
            </template>

            <button type="button" @click="newRow++"
                    class="text-indigo-600 text-sm hover:underline mt-2">
                + Add Value
            </button>

            <div class="flex gap-2 mt-6 pt-4 border-t">
                <button class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">Create</button>
                <a href="{{ route('admin.attributes.index') }}" class="px-6 py-2 rounded border">Cancel</a>
            </div>
        </form>
    </div>
@endsection