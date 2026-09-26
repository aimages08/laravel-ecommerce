@extends('layouts.shop')

@section('title', 'Order ' . $order->order_number)

@section('content')
    <a href="{{ route('shop.my-account') }}" class="text-indigo-600 hover:underline mb-4 inline-block">
        ← Back to My Account
    </a>

    <div class="flex gap-2 mb-4">
        <a href="{{ route('shop.order.invoice', $order->order_number) }}"
        class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">
            📄 Download Invoice
        </a>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-bold">Order {{ $order->order_number }}</h1>
            <span class="text-xs px-3 py-1 rounded bg-yellow-100 text-yellow-800">
                {{ ucfirst($order->status) }}
            </span>
        </div>
        <p class="text-sm text-gray-500 mb-6">Placed on {{ $order->created_at->format('M d, Y H:i') }}</p>

                <div class="text-sm mb-4">
            <span class="text-gray-500">Payment:</span>
            <strong>{{ ucfirst($order->payment_status) }}</strong>
            <span class="text-gray-400">({{ strtoupper($order->payment_method) }})</span>
        </div>

        <h2 class="font-bold mb-3">Items</h2>
        <table class="w-full text-sm mb-6">
            <thead class="text-left text-gray-500 border-b">
                <tr>
                    <th class="pb-2">Product</th>
                    <th class="pb-2">Price</th>
                    <th class="pb-2">Qty</th>
                    <th class="pb-2 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr class="border-b">
                        <td class="py-2">{{ $item->product_name }}</td>
                        <td class="py-2">Rs. {{ number_format($item->price) }}</td>
                        <td class="py-2">{{ $item->quantity }}</td>
                        <td class="py-2 text-right">Rs. {{ number_format($item->subtotal) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="text-right text-sm space-y-1">
            <p>Subtotal: Rs. {{ number_format($order->subtotal) }}</p>
            @if ($order->discount > 0)
                <p class="text-green-600">Discount: - Rs. {{ number_format($order->discount) }}</p>
            @endif
            <p>Shipping: Rs. {{ number_format($order->shipping) }}</p>
            <p class="text-lg font-bold">Total: Rs. {{ number_format($order->total) }}</p>
        </div>

        @if ($order->address)
            <div class="mt-6 border-t pt-4">
                <h2 class="font-bold mb-2">Shipping Address</h2>
                <p class="text-sm">{{ $order->address->name }} — {{ $order->address->phone }}</p>
                <p class="text-sm text-gray-600">{{ $order->address->address_line1 }}</p>
                <p class="text-sm text-gray-600">{{ $order->address->city }}, {{ $order->address->state }}</p>
            </div>
        @endif


        @if (in_array($order->status, ['pending', 'confirmed']))
        <div class="mt-6 pt-4 border-t">
            <form action="{{ route('shop.order.cancel', $order) }}" method="POST"
                onsubmit="return confirm('Cancel this order?')">
                @csrf @method('PATCH')
                <button class="bg-red-600 text-white px-5 py-2 rounded hover:bg-red-700 text-sm">
                    Cancel Order
                </button>
            </form>
        </div>
    @endif


    {{-- STATUS TIMELINE --}}
    <div class="my-8">
        @php $steps = $order->statusSteps(); @endphp

        @if (in_array($order->status, ['cancelled', 'returned']))
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                <p class="font-bold text-red-700">Order {{ ucfirst($order->status) }}</p>
                @if ($order->cancelled_at)
                    <p class="text-xs text-red-600 mt-1">on {{ $order->cancelled_at->format('M d, Y H:i') }}</p>
                @endif
            </div>
        @else
            <div class="flex items-center justify-between relative">
                {{-- Progress bar background --}}
                <div class="absolute top-4 left-0 right-0 h-1 bg-gray-200 z-0"></div>

                {{-- Progress fill --}}
                @php
                    $doneCount = collect($steps)->where('done', true)->count();
                    $fillPct = $doneCount > 1 ? (($doneCount - 1) / (count($steps) - 1)) * 100 : 0;
                @endphp
                <div class="absolute top-4 left-0 h-1 bg-indigo-600 z-0 transition-all"
                    style="width: {{ $fillPct }}%"></div>

                @foreach ($steps as $step)
                    <div class="relative z-10 flex flex-col items-center flex-1">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold
                            {{ $step['done'] ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                            @if ($step['done']) ✓ @else • @endif
                        </div>
                        <p class="text-xs mt-2 {{ $step['current'] ? 'font-bold text-indigo-600' : 'text-gray-600' }}">
                            {{ ucfirst($step['name']) }}
                        </p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    </div>
@endsection