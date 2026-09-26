@extends('layouts.admin')

@section('title', 'Settings')

@section('content')

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @foreach ($settings as $group => $items)
            <div class="bg-white rounded-xl shadow p-6 mb-6">
                <h2 class="font-bold text-lg mb-4 capitalize">{{ $group }} Settings</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($items as $s)
                        <div class="{{ in_array($s->type, ['image']) ? 'md:col-span-2' : '' }}">
                            <label class="block mb-1 text-sm font-medium capitalize">
                                {{ str_replace('_', ' ', $s->key) }}
                            </label>

                            @if ($s->type === 'bool')
                                <select name="{{ $s->key }}" class="w-full border rounded px-3 py-2">
                                    <option value="1" {{ $s->value == '1' ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ $s->value == '0' ? 'selected' : '' }}>No</option>
                                </select>

                            @elseif ($s->type === 'number')
                                <input type="number" step="0.01" name="{{ $s->key }}"
                                       value="{{ $s->value }}"
                                       class="w-full border rounded px-3 py-2">

                            @elseif ($s->type === 'image')
                                <div class="flex items-center gap-4">
                                    @if ($s->value)
                                        <img src="{{ str_starts_with($s->value, 'http') ? $s->value : asset('storage/' . $s->value) }}"
                                             class="h-16 w-16 object-contain border rounded bg-gray-50 p-1">
                                    @endif
                                    <input type="file" name="{{ $s->key }}" accept="image/*"
                                           class="flex-1 border rounded px-3 py-2">
                                </div>

                            @else
                                <input type="text" name="{{ $s->key }}"
                                       value="{{ $s->value }}"
                                       class="w-full border rounded px-3 py-2">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="flex gap-2">
            <button class="bg-indigo-600 text-white px-6 py-3 rounded hover:bg-indigo-700 font-semibold">
                Save Settings
            </button>
        </div>
    </form>
@endsection