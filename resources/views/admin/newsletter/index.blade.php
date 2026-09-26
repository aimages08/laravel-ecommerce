@extends('layouts.admin')

@section('title', 'Newsletter Subscribers')

@section('content')

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-500 text-sm">Active Subscribers</p>
            <p class="text-2xl font-bold mt-1">{{ $totalActive }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-500 text-sm">Unsubscribed</p>
            <p class="text-2xl font-bold mt-1">{{ $totalInactive }}</p>
        </div>
    </div>

    <div class="bg-white p-4 rounded-xl shadow mb-4 flex flex-wrap gap-3 items-center justify-between">
        <form method="GET" class="flex gap-3 items-center">
            <label class="text-sm">Filter:</label>
            <select name="filter" class="border rounded px-3 py-2 text-sm" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="active"   {{ request('filter') === 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('filter') === 'inactive' ? 'selected' : '' }}>Unsubscribed</option>
            </select>
        </form>

        <a href="{{ route('admin.newsletter.export') }}"
           class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">
            📥 Export CSV
        </a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3">Email</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Subscribed At</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($subscribers as $s)
                    <tr class="border-t">
                        <td class="p-3">{{ $s->email }}</td>
                        <td class="p-3">
                            @if ($s->is_subscribed)
                                <span class="text-xs px-2 py-1 rounded bg-green-100 text-green-700">Active</span>
                            @else
                                <span class="text-xs px-2 py-1 rounded bg-red-100 text-red-700">Unsubscribed</span>
                            @endif
                        </td>
                        <td class="p-3 text-gray-500">{{ $s->created_at->format('M d, Y H:i') }}</td>
                        <td class="p-3 text-right">
                            <form action="{{ route('admin.newsletter.destroy', $s) }}" method="POST"
                                  onsubmit="return confirm('Remove this subscriber?')" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-red-600 text-xs hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-4 text-center text-gray-500">No subscribers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $subscribers->links() }}</div>
@endsection