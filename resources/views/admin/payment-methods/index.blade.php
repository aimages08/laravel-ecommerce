@extends('layouts.admin')
@section('title', 'Payment Methods')
@section('content')

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Payment Methods</h2>
        <a href="{{ route('admin.payment-methods.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">+ Add Method</a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3">Name</th>
                    <th class="p-3">Code</th>
                    <th class="p-3">Credentials</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Mode</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($methods as $m)
                    <tr class="border-t">
                        <td class="p-3 font-medium">{{ $m->name }}</td>
                        <td class="p-3 text-xs text-gray-500 font-mono">{{ $m->code }}</td>
                        <td class="p-3 text-xs text-gray-500">
                            {{ $m->settings->count() }} field(s)
                        </td>
                        <td class="p-3">
                            <form action="{{ route('admin.payment-methods.toggle', $m) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="text-xs px-2 py-1 rounded
                                    {{ $m->is_enabled ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $m->is_enabled ? 'Enabled' : 'Disabled' }}
                                </button>
                            </form>
                        </td>
                        <td class="p-3 text-xs">
                            {{ $m->is_test_mode ? '🧪 Test' : '🔴 Live' }}
                        </td>
                        <td class="p-3 text-right space-x-2">
                            <a href="{{ route('admin.payment-methods.edit', $m) }}"
                               class="text-indigo-600 hover:underline">Configure</a>
                            @if (!in_array($m->code, ['cod']))
                                <form action="{{ route('admin.payment-methods.destroy', $m) }}"
                                      method="POST" class="inline"
                                      onsubmit="return confirm('Delete this method?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-4 text-center text-gray-500">No methods yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection