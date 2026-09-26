@extends('layouts.admin')

@section('title', 'Order ' . $order->order_number)

@section('content')

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4">
        <a href="{{ route('admin.orders.index') }}" class="text-indigo-600 hover:underline">
            ← Back to Orders
        </a>
    </div>

    <div class="flex gap-2 mb-4">
    <a href="{{ route('admin.orders.invoice', $order) }}"
       class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">
        📄 Download Invoice
    </a>
       <a href="{{ route('admin.orders.packing-slip', $order) }}"
            class="bg-gray-800 text-white px-4 py-2 rounded text-sm hover:bg-gray-900 border border-gray-600">
                📦 Packing Slip
            </a>
    </div>

    <div class="grid md:grid-cols-3 gap-6">

        {{-- Left: Items + Address --}}
        <div class="md:col-span-2 space-y-6">

            {{-- Items --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="font-bold text-lg mb-4">Items</h2>
                <table class="w-full text-sm">
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

                <div class="mt-4 text-right space-y-1 text-sm">
                    <p>Subtotal: Rs. {{ number_format($order->subtotal) }}</p>
                    <p>Shipping: Rs. {{ number_format($order->shipping) }}</p>
                    <p class="font-bold text-lg">Total: Rs. {{ number_format($order->total) }}</p>
                </div>
            </div>

            {{-- Address --}}
            @if ($order->address)
                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="font-bold text-lg mb-4">Shipping Address</h2>
                    <p><strong>{{ $order->address->name }}</strong></p>
                    <p>{{ $order->address->phone }}</p>
                    @if ($order->address->email)<p>{{ $order->address->email }}</p>@endif
                    <p class="mt-2">{{ $order->address->address_line1 }}</p>
                    @if ($order->address->address_line2)<p>{{ $order->address->address_line2 }}</p>@endif
                    <p>{{ $order->address->city }}, {{ $order->address->state }} {{ $order->address->postal_code }}</p>
                    <p>{{ $order->address->country }}</p>
                </div>
            @endif
        </div>

        {{-- Right: Status --}}
        <div class="space-y-6">

            {{-- Order Status --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="font-bold text-lg mb-4">Order Status</h2>

                <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="space-y-3">
                    @csrf @method('PATCH')

                    <select name="status" class="w-full border rounded px-3 py-2">
                        @foreach (['pending','confirmed','processing','shipped','delivered','cancelled','returned'] as $s)
                            <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>

                    <input type="text" name="tracking_number" placeholder="Tracking # (optional)"
                           value="{{ $order->tracking_number }}"
                           class="w-full border rounded px-3 py-2">
                    <input type="text" name="carrier" placeholder="Carrier (optional)"
                           value="{{ $order->carrier }}"
                           class="w-full border rounded px-3 py-2">

                    <button class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">
                        Update Status
                    </button>
                </form>

                @if ($order->shipped_at)
                    <p class="text-xs text-gray-500 mt-2">Shipped: {{ $order->shipped_at->format('M d, Y') }}</p>
                @endif
                @if ($order->delivered_at)
                    <p class="text-xs text-gray-500">Delivered: {{ $order->delivered_at->format('M d, Y') }}</p>
                @endif
            </div>

            {{-- Payment Status --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-bold text-lg mb-4">Payment</h2>

            <p class="text-sm mb-2">
                Method: <strong>
                    @php
                        $pm = \App\Models\PaymentMethod::where('code', $order->payment_method)->first();
                    @endphp
                    {{ $pm->name ?? strtoupper($order->payment_method) }}
                </strong>
                @if ($pm && $pm->is_test_mode)
                    <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded ml-2">TEST</span>
                @endif
            </p>

            <form action="{{ route('admin.orders.payment', $order) }}" method="POST" class="space-y-3">
                @csrf @method('PATCH')

                <select name="payment_status" class="w-full border rounded px-3 py-2">
                    @foreach (['unpaid','paid','refunded'] as $s)
                        <option value="{{ $s }}" {{ $order->payment_status === $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>

                <button class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">
                    Update Payment
                </button>
            </form>

            {{-- Payment history --}}
            @if ($order->payments->count())
                <div class="mt-4 pt-4 border-t">
                    <h3 class="text-sm font-semibold mb-2">Payment Records</h3>
                    @foreach ($order->payments as $p)
                        <div class="text-xs border-b py-2">
                            <div class="flex justify-between">
                                <span class="font-medium">{{ strtoupper($p->gateway) }}</span>
                                <span class="text-xs px-2 py-0.5 rounded
                                    @if($p->status === 'paid') bg-green-100 text-green-700
                                    @elseif($p->status === 'failed') bg-red-100 text-red-700
                                    @elseif($p->status === 'refunded') bg-orange-100 text-orange-700
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </div>
                            <p class="text-gray-500 mt-1">
                                Rs. {{ number_format($p->amount) }} {{ $p->currency }}
                            </p>
                            @if ($p->transaction_id)
                                <p class="text-gray-400">TXN: {{ $p->transaction_id }}</p>
                            @endif
                            <p class="text-gray-400">{{ $p->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

            {{-- Customer Info --}}
            @if ($order->user)
                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="font-bold text-lg mb-3">Customer</h2>
                    <p class="text-sm">{{ $order->user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $order->user->email }}</p>
                </div>
            @endif
        </div>
    </div>

@endsection