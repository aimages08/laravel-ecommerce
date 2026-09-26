@extends('layouts.admin')
@section('title', 'Attributes')
@section('content')

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Product Attributes</h2>
        <a href="{{ route('admin.attributes.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">+ Add Attribute</a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3">Name</th>
                    <th class="p-3">Type</th>
                    <th class="p-3">Values</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($attributes as $attr)
                    <tr class="border-t">
                        <td class="p-3 font-medium">{{ $attr->name }}</td>
                        <td class="p-3">
                            <span class="text-xs px-2 py-1 rounded bg-indigo-100 text-indigo-700">
                                {{ $attr->type }}
                            </span>
                        </td>
                        <td class="p-3 text-xs text-gray-500">
                            {{ $attr->values->pluck('value')->implode(', ') ?: '—' }}
                        </td>
                        <td class="p-3">
                            <form action="{{ route('admin.attributes.toggle', $attr) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="text-xs px-2 py-1 rounded
                                    {{ $attr->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $attr->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="p-3 text-right space-x-2">
                            <a href="{{ route('admin.attributes.edit', $attr) }}"
                               class="text-indigo-600 hover:underline">Edit</a>
                            <form action="{{ route('admin.attributes.destroy', $attr) }}" method="POST"
                                  class="inline" onsubmit="return confirm('Delete attribute? All product variants using this attribute will be removed.')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-4 text-center text-gray-500">No attributes yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $attributes->links() }}</div>
@endsection