@extends('layouts.admin')
@section('title', 'Configure ' . $method->name)
@section('content')

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-xl shadow max-w-4xl" x-data="{ newRow: 0 }">
        <form action="{{ route('admin.payment-methods.update', $method) }}" method="POST">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block mb-1 font-medium">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $method->name) }}" required
                           class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block mb-1 font-medium">Code *</label>
                    <input type="text" name="code" value="{{ old('code', $method->code) }}" required
                           class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="mb-4 grid grid-cols-3 gap-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_enabled" value="1"
                           {{ $method->is_enabled ? 'checked' : '' }} class="mr-2"> Enabled
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_test_mode" value="1"
                           {{ $method->is_test_mode ? 'checked' : '' }} class="mr-2"> Test Mode
                </label>
                <div>
                    <input type="number" name="sort_order" value="{{ $method->sort_order }}"
                           placeholder="Sort order"
                           class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Instructions (shown to customer)</label>
                <textarea name="instructions" rows="3"
                          class="w-full border rounded px-3 py-2">{{ old('instructions', $method->instructions) }}</textarea>
            </div>

            {{-- Existing credentials --}}
            <h3 class="font-bold mt-6 mb-3 border-t pt-4">Credentials</h3>

            @if ($method->settings->count())
                <div class="space-y-3">
                    @foreach ($method->settings as $s)
                        <div class="border rounded p-3 bg-gray-50">
                            <div class="grid grid-cols-12 gap-2 items-center">
                                <div class="col-span-4">
                                    <label class="text-xs text-gray-500">Key</label>
                                    <input type="text" value="{{ $s->key }}" disabled
                                           class="w-full border rounded px-2 py-1 bg-gray-100 text-sm font-mono">
                                </div>

                                <div class="col-span-6">
                                    <label class="text-xs text-gray-500">
                                        Value
                                        @if ($s->field_type && $s->field_type !== 'text')
                                            <span class="text-indigo-500">({{ $s->field_type }})</span>
                                        @endif
                                    </label>

                                    @if ($s->field_type === 'bool')
                                        <select name="settings[{{ $s->id }}]"
                                                class="w-full border rounded px-2 py-1 text-sm">
                                            <option value="1" {{ $s->value == '1' ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ $s->value == '0' ? 'selected' : '' }}>No</option>
                                        </select>

                                    @elseif ($s->field_type === 'password')
                                        <input type="password" name="settings[{{ $s->id }}]"
                                               placeholder="{{ $s->value ? '•••••• (leave blank to keep)' : 'Enter value' }}"
                                               class="w-full border rounded px-2 py-1 text-sm">

                                    @elseif ($s->field_type === 'number')
                                        <input type="number" name="settings[{{ $s->id }}]"
                                               value="{{ $s->is_encrypted ? '' : $s->value }}"
                                               placeholder="Enter number"
                                               class="w-full border rounded px-2 py-1 text-sm">

                                    @else
                                        <input type="text" name="settings[{{ $s->id }}]"
                                               value="{{ $s->is_encrypted ? '' : $s->value }}"
                                               placeholder="{{ $s->is_encrypted && $s->value ? '•••••• (leave blank to keep)' : 'Enter value' }}"
                                               class="w-full border rounded px-2 py-1 text-sm">
                                    @endif
                                </div>

                                <div class="col-span-2 flex items-center gap-2 text-xs">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="encrypt[{{ $s->id }}]" value="1"
                                               {{ $s->is_encrypted ? 'checked' : '' }} class="mr-1">
                                        Encrypt
                                    </label>
                                </div>
                            </div>

                            <div class="text-right mt-2">
                                <button type="button"
                                        onclick="if(confirm('Remove this field?')) document.getElementById('del-{{ $s->id }}').submit();"
                                        class="text-red-600 text-xs hover:underline">
                                    Remove
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm mb-4">No credentials yet. Add below.</p>
            @endif

            {{-- Add new credential rows --}}
            <h3 class="font-bold mt-6 mb-3 border-t pt-4">Add New Credential</h3>

            <template x-for="i in newRow" :key="i">
                <div class="grid grid-cols-12 gap-2 items-center mb-2">
                    <input type="text" name="new_keys[]" placeholder="Key (e.g. secret_key)"
                           class="col-span-3 border rounded px-2 py-1 text-sm font-mono">

                    <select name="new_types[]" class="col-span-2 border rounded px-2 py-1 text-sm">
                        <option value="text">Text</option>
                        <option value="bool">Yes/No</option>
                        <option value="number">Number</option>
                        <option value="password">Password</option>
                    </select>

                    <input type="text" name="new_values[]" placeholder="Value"
                           class="col-span-5 border rounded px-2 py-1 text-sm">

                    <label class="col-span-2 inline-flex items-center text-xs">
                        <input type="checkbox" name="new_encrypt[]" value="1" class="mr-1">
                        Encrypt
                    </label>
                </div>
            </template>

            <button type="button" @click="newRow++"
                    class="text-indigo-600 text-sm hover:underline mt-2">
                + Add Credential Field
            </button>

            <div class="flex gap-2 mt-6 pt-4 border-t">
                <button class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">Save</button>
                <a href="{{ route('admin.payment-methods.index') }}" class="px-6 py-2 rounded border">Cancel</a>
            </div>
        </form>
    </div>

    {{-- Hidden delete forms --}}
    @foreach ($method->settings as $s)
        <form id="del-{{ $s->id }}"
              action="{{ route('admin.payment-methods.setting.delete', $s) }}"
              method="POST" class="hidden">
            @csrf @method('DELETE')
        </form>
    @endforeach
@endsection