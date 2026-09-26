@extends('layouts.admin')
@section('title', 'Edit ' . $attribute->name)
@section('content')

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-xl shadow max-w-3xl" x-data="{ newRow: 0 }">
        <form action="{{ route('admin.attributes.update', $attribute) }}" method="POST">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block mb-1 font-medium">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $attribute->name) }}" required
                           class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block mb-1 font-medium">Type *</label>
                    <select name="type" class="w-full border rounded px-3 py-2">
                        <option value="select" {{ $attribute->type === 'select' ? 'selected' : '' }}>Select</option>
                        <option value="color"  {{ $attribute->type === 'color' ? 'selected' : '' }}>Color</option>
                        <option value="text"   {{ $attribute->type === 'text' ? 'selected' : '' }}>Text</option>
                    </select>
                </div>
            </div>

            <div class="mb-4 grid grid-cols-2 gap-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1"
                           {{ $attribute->is_active ? 'checked' : '' }} class="mr-2"> Active
                </label>
                <input type="number" name="sort_order" value="{{ $attribute->sort_order }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            {{-- Existing values --}}
            <h3 class="font-bold mt-6 mb-3 border-t pt-4">Values</h3>

            @if ($attribute->values->count())
                @foreach ($attribute->values as $v)
                    <div class="flex items-center gap-2 mb-2">
                        <input type="text" name="values[{{ $v->id }}]" value="{{ $v->value }}"
                               class="flex-1 border rounded px-2 py-1 text-sm">

                        @if ($attribute->type === 'color')
                            <input type="color" name="colors[{{ $v->id }}]"
                                   value="{{ $v->color_code ?? '#000000' }}"
                                   class="border rounded h-9 w-20">
                        @endif

                        <button type="button"
                                onclick="if(confirm('Remove this value?')) document.getElementById('del-{{ $v->id }}').submit();"
                                class="text-red-600 text-xs hover:underline">
                            Remove
                        </button>
                    </div>
                @endforeach
            @else
                <p class="text-gray-500 text-sm">No values yet.</p>
            @endif

            {{-- Add new values --}}
            <h3 class="font-bold mt-6 mb-3 border-t pt-4">Add New Value(s)</h3>

            <template x-for="i in newRow" :key="i">
                <div class="flex items-center gap-2 mb-2">
                    <input type="text" name="new_values[]"
                           placeholder="Value (e.g. S, M, Red)"
                           class="flex-1 border rounded px-2 py-1 text-sm">

                    @if ($attribute->type === 'color')
                        <input type="color" name="new_colors[]" value="#000000"
                               class="border rounded h-9 w-20">
                    @endif
                </div>
            </template>

            <button type="button" @click="newRow++"
                    class="text-indigo-600 text-sm hover:underline mt-2">
                + Add Value Field
            </button>

            <div class="flex gap-2 mt-6 pt-4 border-t">
                <button class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">Save</button>
                <a href="{{ route('admin.attributes.index') }}" class="px-6 py-2 rounded border">Cancel</a>
            </div>
        </form>
    </div>

    @foreach ($attribute->values as $v)
        <form id="del-{{ $v->id }}" action="{{ route('admin.attributes.value.delete', $v) }}"
              method="POST" class="hidden">
            @csrf @method('DELETE')
        </form>
    @endforeach
@endsection