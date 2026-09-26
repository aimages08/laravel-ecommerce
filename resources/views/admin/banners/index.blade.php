@extends('layouts.admin')

@section('title', 'Banners')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">All Banners</h2>
        <a href="{{ route('admin.banners.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">+ Add Banner</a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3">Image</th>
                    <th class="p-3">Title</th>
                    <th class="p-3">Type</th>
                    <th class="p-3">Schedule</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($banners as $b)
                    <tr class="border-t">
                        <td class="p-3">
                            @if ($b->image_url)
                                <img src="{{ $b->image_url }}" class="h-12 w-20 object-cover rounded">
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="p-3">
                            <p class="font-semibold">{{ $b->title ?? '(no title)' }}</p>
                            <p class="text-xs text-gray-500">{{ $b->subtitle }}</p>
                        </td>
                        <td class="p-3">
                            <span class="text-xs px-2 py-1 rounded bg-indigo-100 text-indigo-700 uppercase">
                                {{ $b->type }}
                            </span>
                        </td>
                        <td class="p-3 text-xs text-gray-500">
                            {{ $b->starts_at?->format('M d') ?? '—' }} →
                            {{ $b->ends_at?->format('M d') ?? '—' }}
                        </td>
                        <td class="p-3">
                            <form action="{{ route('admin.banners.toggle', $b) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="text-xs px-2 py-1 rounded {{ $b->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $b->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="p-3 text-right space-x-2">
                            <a href="{{ route('admin.banners.edit', $b) }}" class="text-indigo-600 hover:underline">Edit</a>
                            <form action="{{ route('admin.banners.destroy', $b) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Delete this banner?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-4 text-center text-gray-500">No banners yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $banners->links() }}</div>
@endsection