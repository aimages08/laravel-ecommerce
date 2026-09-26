@extends('layouts.shop')

@section('title', 'Contact Us')

@section('content')
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-2 text-center">Contact Us</h1>
        <p class="text-gray-500 text-center mb-8">We'd love to hear from you</p>

        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow text-center">
                <div class="text-3xl mb-2">📧</div>
                <h3 class="font-bold mb-1">Email</h3>
                <p class="text-sm text-gray-600">{{ setting('store_email', 'store@example.com') }}</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow text-center">
                <div class="text-3xl mb-2">📞</div>
                <h3 class="font-bold mb-1">Phone</h3>
                <p class="text-sm text-gray-600">{{ setting('store_phone', '0300-0000000') }}</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow text-center">
                <div class="text-3xl mb-2">📍</div>
                <h3 class="font-bold mb-1">Address</h3>
                <p class="text-sm text-gray-600">{{ setting('store_address', 'Pakistan') }}</p>
            </div>
        </div>

        <div class="bg-white p-8 rounded-xl shadow">
           

            <form action="{{ route('shop.contact.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-medium">Name *</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}"
                               required class="w-full border rounded px-3 py-2">
                        @error('name')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Email *</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}"
                               required class="w-full border rounded px-3 py-2">
                        @error('email')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-medium">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Subject *</label>
                        <input type="text" name="subject" value="{{ old('subject') }}"
                               required class="w-full border rounded px-3 py-2">
                        @error('subject')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Message *</label>
                    <textarea name="message" rows="5" required
                              class="w-full border rounded px-3 py-2">{{ old('message') }}</textarea>
                    @error('message')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                </div>

                <button class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 font-semibold">
                    Send Message
                </button>
            </form>
        </div>
    </div>
@endsection