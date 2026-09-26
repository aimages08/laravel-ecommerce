@extends('layouts.shop')

@section('title', 'Checkout')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Checkout</h1>

    <form action="{{ route('shop.checkout.store') }}" method="POST" class="grid md:grid-cols-3 gap-6">
        @csrf

        <div class="md:col-span-2 bg-white rounded-xl shadow p-6 space-y-4">
            <h2 class="font-bold text-lg mb-2">Shipping Information</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1">Name *</label>
                    <input type="text" name="name"
                           value="{{ old('name', $defaultAddress->name ?? auth()->user()->name ?? '') }}"
                           class="w-full border rounded px-3 py-2">
                    @error('name')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block mb-1">Phone *</label>
                    <input type="text" name="phone"
                           value="{{ old('phone', $defaultAddress->phone ?? auth()->user()->phone ?? '') }}"
                           class="w-full border rounded px-3 py-2">
                    @error('phone')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block mb-1">Email {{ auth()->check() ? '' : '*' }}</label>
                <input type="email" name="email"
                       value="{{ old('email', auth()->user()->email ?? '') }}"
                       class="w-full border rounded px-3 py-2">
                @error('email')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
            </div>

            {{-- Create Account (only for guests) --}}
            @guest
                <div class="bg-indigo-50 border border-indigo-200 rounded p-4">
                    <label class="inline-flex items-center font-medium">
                        <input type="checkbox" name="create_account" value="1"
                               id="create_account" class="mr-2"
                               {{ old('create_account') ? 'checked' : '' }}>
                        Create an account for faster checkout
                    </label>
                    <p class="text-xs text-gray-500 mt-1 ml-6">
                        We'll create your account with the email above.
                    </p>

                    <div id="password-fields" class="mt-3 {{ old('create_account') ? '' : 'hidden' }}">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-1 text-sm">Password *</label>
                                <input type="password" name="password"
                                       class="w-full border rounded px-3 py-2">
                                @error('password')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block mb-1 text-sm">Confirm Password *</label>
                                <input type="password" name="password_confirmation"
                                       class="w-full border rounded px-3 py-2">
                            </div>
                        </div>
                    </div>
                </div>
            @endguest

            <div>
                <label class="block mb-1">Address Line 1 *</label>
                <input type="text" name="address_line1"
                       value="{{ old('address_line1', $defaultAddress->address_line1 ?? '') }}"
                       class="w-full border rounded px-3 py-2">
                @error('address_line1')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block mb-1">Address Line 2</label>
                <input type="text" name="address_line2"
                       value="{{ old('address_line2', $defaultAddress->address_line2 ?? '') }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block mb-1">City *</label>
                    <input type="text" name="city"
                           value="{{ old('city', $defaultAddress->city ?? '') }}"
                           class="w-full border rounded px-3 py-2">
                    @error('city')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block mb-1">State</label>
                    <input type="text" name="state"
                           value="{{ old('state', $defaultAddress->state ?? '') }}"
                           class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block mb-1">Postal Code</label>
                    <input type="text" name="postal_code"
                           value="{{ old('postal_code', $defaultAddress->postal_code ?? '') }}"
                           class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div>
                <label class="block mb-1">Order Notes</label>
                <textarea name="notes" rows="2"
                          class="w-full border rounded px-3 py-2">{{ old('notes') }}</textarea>
            </div>

            <h2 class="font-bold text-lg mt-4">Payment Method</h2>

                @if ($paymentMethods->isEmpty())
                    <div class="bg-yellow-50 border border-yellow-200 rounded p-3 text-sm text-yellow-800">
                        No payment methods are available. Please contact support.
                    </div>
                @else
                    <div class="space-y-2">
                        @foreach ($paymentMethods as $pm)
                            <label class="flex items-start gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50
                                        has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50">
                                <input type="radio" name="payment_method" value="{{ $pm->code }}"
                                    {{ $loop->first ? 'checked' : '' }} class="mt-1"
                                    onchange="document.getElementById('pm-{{ $pm->id }}-info').classList.toggle('hidden', !this.checked); document.querySelectorAll('[id^=pm-][id$=-info]').forEach(el => el.id !== 'pm-{{ $pm->id }}-info' && el.classList.add('hidden'))">
                                <div class="flex-1">
                                    <p class="font-medium">{{ $pm->name }}</p>
                                    @if ($pm->instructions)
                                        <div id="pm-{{ $pm->id }}-info"
                                            class="{{ $loop->first ? '' : 'hidden' }} text-xs text-gray-600 mt-1 whitespace-pre-line">
                                            {{ $pm->instructions }}
                                        </div>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('payment_method')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                @endif


        </div>

        <div class="bg-white rounded-xl shadow p-6 h-fit">
            <h2 class="font-bold text-lg mb-4">Order Summary</h2>

            @foreach ($cart as $item)
                <div class="flex justify-between py-2 border-b text-sm">
                    <span>{{ $item['name'] }} × {{ $item['quantity'] }}</span>
                    <span>Rs. {{ number_format($item['price'] * $item['quantity']) }}</span>
                </div>
            @endforeach

            <div class="flex justify-between pt-3 text-sm">
                <span>Subtotal</span>
                <span>Rs. {{ number_format($subtotal) }}</span>
            </div>

            @if ($discount > 0)
                <div class="flex justify-between text-sm text-green-600">
                    <span>Discount ({{ $coupon->code }})</span>
                    <span>- Rs. {{ number_format($discount) }}</span>
                </div>
            @endif

            <div class="flex justify-between text-sm">
                <span>Shipping</span>
                <span>
                    @if ($shipping == 0)
                        <span class="text-green-600">Free</span>
                    @else
                        Rs. {{ number_format($shipping) }}
                    @endif
                </span>
            </div>

            <div class="flex justify-between pt-3 mt-3 border-t text-lg font-bold">
                <span>Total</span>
                <span class="text-indigo-600">Rs. {{ number_format($total) }}</span>
            </div>

            <button class="w-full mt-4 bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 font-semibold">
                Place Order
            </button>
        </div>
    </form>

    <script>
    const checkbox = document.getElementById('create_account');
    const fields = document.getElementById('password-fields');
    if (checkbox) {
        checkbox.addEventListener('change', () => {
            fields.classList.toggle('hidden', !checkbox.checked);
            if (!checkbox.checked) {
                fields.querySelectorAll('input').forEach(i => i.value = '');
            }
        });
    }
    </script>
@endsection