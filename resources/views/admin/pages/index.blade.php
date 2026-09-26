@extends('layouts.admin')
@section('title', 'Pages')
@section('content')
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">CMS Pages</h2>
        <a href="{{ route('admin.pages.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">+ Add Page</a>
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
                    <th class="p-3">Title</th>
                    <th class="p-3">Slug</th>
                    <th class="p-3">Footer</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pages as $p)
                    <tr class="border-t">
                        <td class="p-3 font-medium">{{ $p->title }}</td>
                        <td class="p-3 text-gray-500">{{ $p->slug }}</td>
                        <td class="p-3">{{ $p->show_in_footer ? 'Yes' : 'No' }}</td>
                        <td class="p-3">
                            <form action="{{ route('admin.pages.toggle', $p) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="text-xs px-2 py-1 rounded {{ $p->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $p->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="p-3 text-right space-x-2">
                            <a href="{{ route('admin.pages.edit', $p) }}" class="text-indigo-600 hover:underline">Edit</a>
                            <form action="{{ route('admin.pages.destroy', $p) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-4 text-center text-gray-500">No pages yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $pages->links() }}</div>
@endsection