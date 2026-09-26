@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" class="bg-white p-4 rounded-xl shadow mb-4 flex gap-3 items-center">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search name, email, phone..."
               class="border rounded px-3 py-2 w-72">
        <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Search</button>
        <a href="{{ route('admin.customers.index') }}" class="text-gray-500 hover:underline text-sm">Reset</a>
    </form>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3">Name</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Phone</th>
                    <th class="p-3">Orders</th>
                    <th class="p-3">Total Spent</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $c)
                    <tr class="border-t">
                        <td class="p-3 font-medium">{{ $c->name }}</td>
                        <td class="p-3 text-gray-500">{{ $c->email }}</td>
                        <td class="p-3 text-gray-500">{{ $c->phone ?? '—' }}</td>
                        <td class="p-3">{{ $c->orders_count }}</td>
                        <td class="p-3 font-semibold text-indigo-600">
                            Rs. {{ number_format($c->total_spent ?? 0) }}
                        </td>
                        <td class="p-3">
                            @if ($c->is_blocked)
                                <span class="text-xs px-2 py-1 rounded bg-red-100 text-red-700">Blocked</span>
                            @else
                                <span class="text-xs px-2 py-1 rounded bg-green-100 text-green-700">Active</span>
                            @endif
                        </td>
                        <td class="p-3 text-right space-x-2">
                            <a href="{{ route('admin.customers.show', $c) }}"
                               class="text-indigo-600 hover:underline">View</a>
                            <form action="{{ route('admin.customers.block', $c) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button class="text-xs {{ $c->is_blocked ? 'text-green-600' : 'text-red-600' }} hover:underline">
                                    {{ $c->is_blocked ? 'Unblock' : 'Block' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-4 text-center text-gray-500">No customers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $customers->links() }}</div>
@endsection