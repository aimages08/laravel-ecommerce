@extends('layouts.shop')

@section('title', 'My Account')

@section('content')
    <h1 class="text-2xl font-bold mb-6">My Account</h1>

    <div x-data="{ tab: 'orders', editId: null }">

        {{-- Tabs --}}
        <div class="border-b mb-6 flex gap-4">
            <button type="button" @click="tab='profile'"
                    :class="tab==='profile' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500'"
                    class="pb-2 border-b-2 font-medium">Profile</button>
            <button type="button" @click="tab='password'"
                    :class="tab==='password' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500'"
                    class="pb-2 border-b-2 font-medium">Change Password</button>
            <button type="button" @click="tab='addresses'"
                    :class="tab==='addresses' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500'"
                    class="pb-2 border-b-2 font-medium">Addresses</button>
            <button type="button" @click="tab='orders'"
                    :class="tab==='orders' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500'"
                    class="pb-2 border-b-2 font-medium">My Orders</button>
        </div>

        {{-- Profile --}}
        <div x-show="tab==='profile'">
            <div class="bg-white p-6 rounded-xl shadow max-w-xl">
                <form action="{{ route('shop.profile.update') }}" method="POST" class="space-y-4">
                    @csrf @method('PUT')
                    <div>
                        <label class="block mb-1 font-medium">Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                               class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                               class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                               class="w-full border rounded px-3 py-2">
                    </div>
                    <button class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">Save</button>
                </form>
            </div>
        </div>

        {{-- Password --}}
        <div x-show="tab==='password'">
            <div class="bg-white p-6 rounded-xl shadow max-w-xl">
                <form action="{{ route('shop.password.update') }}" method="POST" class="space-y-4">
                    @csrf @method('PUT')
                    <div>
                        <label class="block mb-1 font-medium">Current Password</label>
                        <input type="password" name="current_password"
                               class="w-full border rounded px-3 py-2">
                        @error('current_password')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">New Password</label>
                        <input type="password" name="password"
                               class="w-full border rounded px-3 py-2">
                        @error('password')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Confirm New Password</label>
                        <input type="password" name="password_confirmation"
                               class="w-full border rounded px-3 py-2">
                    </div>
                    <button class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">Update Password</button>
                </form>
            </div>
        </div>

        {{-- Addresses --}}
        <div x-show="tab==='addresses'">
            <div class="grid md:grid-cols-2 gap-6">

                <div class="bg-white p-6 rounded-xl shadow">
                    <h2 class="font-bold mb-4">My Addresses</h2>
                    @forelse ($addresses as $addr)
                        <div class="border rounded p-3 mb-3">

                            <div class="flex justify-between items-start">
                                <p class="font-semibold">
                                    {{ $addr->label ?: 'Address' }}
                                    @if ($addr->is_default)
                                        <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded">Default</span>
                                    @endif
                                </p>
                                <div class="flex gap-2">
                                    <button type="button"
                                            @click="editId = (editId === {{ $addr->id }} ? null : {{ $addr->id }})"
                                            class="text-indigo-600 text-xs">Edit</button>

                                    <form action="{{ route('shop.address.delete', $addr) }}" method="POST"
                                          onsubmit="return confirm('Delete?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 text-xs">Delete</button>
                                    </form>
                                </div>
                            </div>

                            <p class="text-sm">{{ $addr->name }} — {{ $addr->phone }}</p>
                            <p class="text-sm text-gray-600">{{ $addr->address_line1 }}</p>
                            <p class="text-sm text-gray-600">{{ $addr->city }}, {{ $addr->state }}</p>

                            {{-- Inline edit form --}}
                            <div x-show="editId === {{ $addr->id }}" class="mt-3 pt-3 border-t" x-cloak>
                                <form action="{{ route('shop.address.update', $addr) }}" method="POST" class="space-y-2">
                                    @csrf @method('PUT')
                                    <input type="text" name="label" value="{{ $addr->label }}" placeholder="Label"
                                           class="w-full border rounded px-3 py-1 text-sm">
                                    <input type="text" name="name" value="{{ $addr->name }}" placeholder="Full Name"
                                           class="w-full border rounded px-3 py-1 text-sm" required>
                                    <input type="text" name="phone" value="{{ $addr->phone }}" placeholder="Phone"
                                           class="w-full border rounded px-3 py-1 text-sm" required>
                                    <input type="text" name="address_line1" value="{{ $addr->address_line1 }}"
                                           class="w-full border rounded px-3 py-1 text-sm" required>
                                    <input type="text" name="address_line2" value="{{ $addr->address_line2 }}"
                                           class="w-full border rounded px-3 py-1 text-sm">
                                    <div class="grid grid-cols-2 gap-2">
                                        <input type="text" name="city" value="{{ $addr->city }}" placeholder="City"
                                               class="w-full border rounded px-3 py-1 text-sm" required>
                                        <input type="text" name="state" value="{{ $addr->state }}" placeholder="State"
                                               class="w-full border rounded px-3 py-1 text-sm">
                                    </div>
                                    <input type="text" name="postal_code" value="{{ $addr->postal_code }}" placeholder="Postal Code"
                                           class="w-full border rounded px-3 py-1 text-sm">
                                    <label class="inline-flex items-center text-xs">
                                        <input type="checkbox" name="is_default" value="1"
                                               {{ $addr->is_default ? 'checked' : '' }} class="mr-2"> Set as default
                                    </label>
                                    <div class="flex gap-2 pt-1">
                                        <button class="bg-indigo-600 text-white px-3 py-1 rounded text-xs">Save</button>
                                        <button type="button" @click="editId = null" class="px-3 py-1 border rounded text-xs">Cancel</button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No addresses yet.</p>
                    @endforelse
                </div>

                <div class="bg-white p-6 rounded-xl shadow">
                    <h2 class="font-bold mb-4">Add New Address</h2>
                    <form action="{{ route('shop.address.store') }}" method="POST" class="space-y-3">
                        @csrf
                        <input type="text" name="label" placeholder="Label (Home, Office)"
                               class="w-full border rounded px-3 py-2">
                        <input type="text" name="name" placeholder="Full Name *" required
                               class="w-full border rounded px-3 py-2">
                        <input type="text" name="phone" placeholder="Phone *" required
                               class="w-full border rounded px-3 py-2">
                        <input type="text" name="address_line1" placeholder="Address Line 1 *" required
                               class="w-full border rounded px-3 py-2">
                        <input type="text" name="address_line2" placeholder="Address Line 2"
                               class="w-full border rounded px-3 py-2">
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" name="city" placeholder="City *" required
                                   class="w-full border rounded px-3 py-2">
                            <input type="text" name="state" placeholder="State"
                                   class="w-full border rounded px-3 py-2">
                        </div>
                        <input type="text" name="postal_code" placeholder="Postal Code"
                               class="w-full border rounded px-3 py-2">
                        <label class="inline-flex items-center text-sm">
                            <input type="checkbox" name="is_default" value="1" class="mr-2"> Set as default
                        </label>
                        <button class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">Add</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Orders --}}
        <div x-show="tab==='orders'">
            @forelse ($orders as $order)
                <div class="bg-white rounded-xl shadow p-5 mb-4">
                    <div class="flex justify-between mb-2">
                        <div>
                            <a href="{{ route('shop.order.detail', $order->order_number) }}"
                               class="font-bold text-indigo-600 hover:underline">
                                {{ $order->order_number }}
                            </a>
                            <p class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs px-2 py-1 rounded
                                @if($order->status === 'delivered') bg-green-100 text-green-700
                                @elseif($order->status === 'cancelled') bg-red-100 text-red-700
                                @elseif($order->status === 'shipped') bg-blue-100 text-blue-700
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                            <p class="font-bold text-indigo-600 mt-1">Rs. {{ number_format($order->total) }}</p>
                        </div>
                    </div>

                    @if (in_array($order->status, ['pending', 'confirmed']))
                        <div class="pt-3 border-t">
                            <form action="{{ route('shop.order.cancel', $order) }}" method="POST"
                                  onsubmit="return confirm('Cancel this order?')">
                                @csrf @method('PATCH')
                                <button class="text-red-600 text-xs hover:underline">Cancel Order</button>
                            </form>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-xl shadow p-8 text-center text-gray-500">
                    No orders yet.
                </div>
            @endforelse
        </div>

    </div>
@endsection