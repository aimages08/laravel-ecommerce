@extends('layouts.shop')

@section('title', 'Order Placed')

@section('content')
    <div class="bg-white rounded-xl shadow p-10 text-center max-w-2xl mx-auto">
        <div class="text-5xl mb-4">✅</div>
        <h1 class="text-2xl font-bold mb-2">Order Placed Successfully!</h1>
        <p class="text-gray-600 mb-6">
            Your order number is <strong>{{ $order->order_number }}</strong>
        </p>

        <div class="text-left bg-gray-50 p-4 rounded-lg mb-6">
            <p><strong>Total:</strong> Rs. {{ number_format($order->total) }}</p>
            <p><strong>Payment:</strong> {{ strtoupper($order->payment_method) }}</p>
            <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
        </div>

        {{-- Payment method instructions --}}
        @php
            $method = \App\Models\PaymentMethod::where('code', $order->payment_method)->first();
        @endphp

        @if ($method && $method->instructions)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-left mb-6">
                <h2 class="font-bold mb-3 text-blue-800">Payment Instructions</h2>
                <div class="text-sm text-gray-700 whitespace-pre-line">{{ $method->instructions }}</div>
            </div>
        @endif

        {{-- Bank details if bank transfer --}}
        @if ($order->payment_method === 'bank' && $method)
            @php
                $bankFields = ['bank_name', 'account_title', 'account_number', 'iban'];
                $hasDetails = false;
                foreach ($bankFields as $f) {
                    if ($method->getSetting($f)) { $hasDetails = true; break; }
                }
            @endphp

            @if ($hasDetails)
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-left mb-6">
                    <h2 class="font-bold mb-3">Bank Account Details</h2>
                    <table class="w-full text-sm">
                        @foreach ($bankFields as $f)
                            @if ($method->getSetting($f))
                                <tr>
                                    <td class="py-1 pr-4 text-gray-500 capitalize">
                                        {{ str_replace('_', ' ', $f) }}:
                                    </td>
                                    <td class="py-1 font-medium">{{ $method->getSetting($f) }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </table>
                    <p class="text-xs text-gray-500 mt-3">
                        Please share the payment receipt after transferring.
                    </p>
                </div>
            @endif
        @endif

        <a href="{{ route('shop.products') }}"
           class="bg-indigo-600 text-white px-6 py-3 rounded-lg inline-block">
            Continue Shopping
        </a>
    </div>
@endsection