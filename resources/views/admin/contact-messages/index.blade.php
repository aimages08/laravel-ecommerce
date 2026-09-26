@extends('layouts.admin')

@section('title', 'Contact Messages')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-4 rounded-xl shadow mb-4 flex gap-3 items-center">
        <label class="text-sm">Filter:</label>
        <a href="{{ route('admin.contact-messages.index') }}"
           class="text-sm px-3 py-1 rounded {{ !request('filter') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600' }}">All</a>
        <a href="{{ route('admin.contact-messages.index', ['filter' => 'unread']) }}"
           class="text-sm px-3 py-1 rounded {{ request('filter') === 'unread' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600' }}">
            Unread ({{ $unreadCount }})
        </a>
        <a href="{{ route('admin.contact-messages.index', ['filter' => 'read']) }}"
           class="text-sm px-3 py-1 rounded {{ request('filter') === 'read' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600' }}">Read</a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3">From</th>
                    <th class="p-3">Subject</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Date</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($messages as $m)
                    <tr class="border-t {{ !$m->is_read ? 'bg-indigo-50' : '' }}">
                        <td class="p-3">
                            <p class="font-semibold">{{ $m->name }}</p>
                            <p class="text-xs text-gray-500">{{ $m->email }}</p>
                        </td>
                        <td class="p-3">{{ $m->subject }}</td>
                        <td class="p-3">
                            @if ($m->is_read)
                                <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-700">Read</span>
                            @else
                                <span class="text-xs px-2 py-1 rounded bg-indigo-100 text-indigo-700">New</span>
                            @endif
                        </td>
                        <td class="p-3 text-gray-500">{{ $m->created_at->format('M d, Y') }}</td>
                        <td class="p-3 text-right space-x-2">
                            <a href="{{ route('admin.contact-messages.show', $m) }}"
                               class="text-indigo-600 hover:underline">View</a>
                            <form action="{{ route('admin.contact-messages.destroy', $m) }}" method="POST"
                                  class="inline" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-4 text-center text-gray-500">No messages.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $messages->links() }}</div>
@endsection