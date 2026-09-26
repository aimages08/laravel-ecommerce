@extends('layouts.shop')

@section('title', 'Cart')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Shopping Cart</h1>

 

    @if (empty($cart))
        <div class="bg-white p-12 rounded-xl shadow text-center">
            <p class="text-gray-500 mb-4">Your cart is empty.</p>
            <a href="{{ route('shop.products') }}"
               class="bg-indigo-600 text-white px-6 py-3 rounded-lg">Shop Now</a>
        </div>
    @else
        <div class="grid md:grid-cols-3 gap-6">

            {{-- Items --}}
            <div class="md:col-span-2 bg-white rounded-xl shadow overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-left">
                        <tr>
                            <th class="p-3">Product</th>
                            <th class="p-3">Price</th>
                            <th class="p-3">Qty</th>
                            <th class="p-3">Subtotal</th>
                            <th class="p-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cart as $id => $item)
                            <tr class="border-t">
                                <td class="p-3 flex items-center gap-3">
                                    @if ($item['image'])
                                        <img src="{{ asset('storage/' . $item['image']) }}"
                                             class="w-12 h-12 object-cover rounded">
                                    @endif
                                    {{ $item['name'] }}
                                </td>
                                <td class="p-3">Rs. {{ number_format($item['price']) }}</td>
                                <td class="p-3">
                                    <form action="{{ route('shop.cart.update', $id) }}" method="POST" class="flex gap-2">
                                        @csrf @method('PATCH')
                                        <input type="number" name="quantity" value="{{ $item['quantity'] }}"
                                               min="1" class="w-16 border rounded px-2 py-1">
                                        <button class="text-xs text-indigo-600">Update</button>
                                    </form>
                                </td>
                                <td class="p-3 font-semibold">
                                    Rs. {{ number_format($item['price'] * $item['quantity']) }}
                                </td>
                                <td class="p-3">
                                    <form action="{{ route('shop.cart.remove', $id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 text-xs">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Summary + Coupon --}}
            <div class="bg-white rounded-xl shadow p-6 h-fit">
                <h2 class="font-bold text-lg mb-4">Order Summary</h2>

                {{-- Coupon --}}
                @if (!$coupon)
                    <form action="{{ route('shop.cart.coupon') }}" method="POST" class="mb-4">
                        @csrf
                        <label class="block mb-1 text-sm font-medium">Have a coupon?</label>
                        <div class="flex gap-2">
                            <input type="text" name="code" placeholder="Enter code"
                                   class="flex-1 border rounded px-3 py-2 uppercase">
                            <button class="bg-gray-800 text-white px-4 py-2 rounded text-sm">Apply</button>
                        </div>
                    </form>
                @else
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded flex justify-between items-center">
                        <div>
                            <p class="text-green-700 text-sm font-semibold">{{ $coupon->code }} applied</p>
                            <p class="text-xs text-green-600">
                                - Rs. {{ number_format($discount) }}
                            </p>
                        </div>
                        <form action="{{ route('shop.cart.coupon.remove') }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-600 hover:underline">Remove</button>
                        </form>
                    </div>
                @endif

                <div class="space-y-2 text-sm border-t pt-3">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span>Rs. {{ number_format($subtotal) }}</span>
                    </div>

                    @if ($discount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Discount</span>
                            <span>- Rs. {{ number_format($discount) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between">
                        <span>Shipping</span>
                        <span>
                            @if ($shipping == 0)
                                <span class="text-green-600">Free</span>
                            @else
                                Rs. {{ number_format($shipping) }}
                            @endif
                        </span>
                    </div>

                    <div class="flex justify-between text-lg font-bold border-t pt-2 mt-2">
                        <span>Total</span>
                        <span class="text-indigo-600">Rs. {{ number_format($total) }}</span>
                    </div>
                </div>

                <a href="{{ route('shop.checkout') }}"
                   class="block mt-4 text-center bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 font-semibold">
                    Proceed to Checkout
                </a>
            </div>
        </div>
    @endif
@endsection