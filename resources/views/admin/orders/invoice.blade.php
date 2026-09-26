<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        .header { display: flex; justify-content: space-between; margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .store-info h1 { margin: 0; color: #4f46e5; font-size: 22px; }
        .store-info p { margin: 2px 0; font-size: 11px; color: #555; }
        .invoice-info { text-align: right; }
        .invoice-info h2 { margin: 0; font-size: 18px; color: #333; }
        .invoice-info p { margin: 2px 0; font-size: 11px; }
        .section { margin-bottom: 18px; }
        .section h3 { font-size: 12px; color: #4f46e5; border-bottom: 1px solid #ccc; padding-bottom: 3px; }
        .grid { width: 100%; }
        .grid td { vertical-align: top; padding: 5px 0; font-size: 11px; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.items th { background: #f3f4f6; padding: 6px; text-align: left; font-size: 11px; border-bottom: 1px solid #ccc; }
        table.items td { padding: 6px; border-bottom: 1px solid #eee; font-size: 11px; }
        table.items td.right, table.items th.right { text-align: right; }
        .totals { width: 250px; margin-left: auto; margin-top: 10px; }
        .totals td { padding: 3px 0; font-size: 12px; }
        .totals .grand { font-weight: bold; border-top: 2px solid #333; font-size: 14px; padding-top: 5px; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #888; border-top: 1px solid #ccc; padding-top: 10px; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 10px; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <div class="store-info">
            <h1>{{ setting('store_name', 'Shoping Website') }}</h1>
            <p>{{ setting('store_address', 'Pakistan') }}</p>
            <p>{{ setting('store_email', 'store@example.com') }}</p>
            <p>{{ setting('store_phone', '0300-0000000') }}</p>
        </div>
        <div class="invoice-info">
            <h2>INVOICE</h2>
            <p><strong>{{ $order->order_number }}</strong></p>
            <p>{{ $order->created_at->format('M d, Y') }}</p>
            <p>Status:
                <span class="badge" style="background:#fef3c7;color:#92400e;">
                    {{ ucfirst($order->status) }}
                </span>
            </p>
            <p>Payment: {{ strtoupper($order->payment_method) }} — {{ ucfirst($order->payment_status) }}</p>
        </div>
    </div>

    <table class="grid">
        <tr>
            <td style="width: 50%;">
                <div class="section">
                    <h3>Bill To</h3>
                    <p><strong>{{ $order->address->name ?? '' }}</strong></p>
                    <p>{{ $order->address->phone ?? '' }}</p>
                    @if ($order->address->email ?? false)
                        <p>{{ $order->address->email }}</p>
                    @endif
                </div>
            </td>
            <td style="width: 50%;">
                <div class="section">
                    <h3>Ship To</h3>
                    <p>{{ $order->address->address_line1 ?? '' }}</p>
                    @if ($order->address->address_line2 ?? false)
                        <p>{{ $order->address->address_line2 }}</p>
                    @endif
                    <p>{{ $order->address->city ?? '' }}, {{ $order->address->state ?? '' }} {{ $order->address->postal_code ?? '' }}</p>
                    <p>{{ $order->address->country ?? 'Pakistan' }}</p>
                </div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th class="right">Price</th>
                <th class="right">Qty</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td class="right">Rs. {{ number_format($item->price, 2) }}</td>
                    <td class="right">{{ $item->quantity }}</td>
                    <td class="right">Rs. {{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>Subtotal</td>
            <td style="text-align: right;">Rs. {{ number_format($order->subtotal, 2) }}</td>
        </tr>
        @if ($order->discount > 0)
            <tr>
                <td>Discount</td>
                <td style="text-align: right; color: #16a34a;">
                    - Rs. {{ number_format($order->discount, 2) }}
                </td>
            </tr>
        @endif
        <tr>
            <td>Shipping</td>
            <td style="text-align: right;">
                @if ($order->shipping == 0) FREE @else Rs. {{ number_format($order->shipping, 2) }} @endif
            </td>
        </tr>
        <tr class="grand">
            <td>Total</td>
            <td style="text-align: right; color: #4f46e5;">
                Rs. {{ number_format($order->total, 2) }}
            </td>
        </tr>
    </table>

    @if ($order->notes)
        <div class="section" style="margin-top: 20px;">
            <h3>Notes</h3>
            <p style="font-size: 11px;">{{ $order->notes }}</p>
        </div>
    @endif

    <div class="footer">
        Thank you for your business! — {{ setting('store_name', 'Shoping Website') }}
    </div>

</body>
</html>