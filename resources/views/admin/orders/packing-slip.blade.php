<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Packing Slip {{ $order->order_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        h1 { color: #4f46e5; margin: 0; }
        .header { border-bottom: 2px solid #4f46e5; padding-bottom: 10px; margin-bottom: 15px; }
        .header p { margin: 2px 0; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f3f4f6; padding: 6px; text-align: left; font-size: 11px; border-bottom: 1px solid #ccc; }
        td { padding: 8px 6px; border-bottom: 1px solid #eee; }
        .check { width: 30px; text-align: center; }
        .info { margin-top: 10px; font-size: 11px; }
        .info p { margin: 3px 0; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Packing Slip</h1>
        <p><strong>Order:</strong> {{ $order->order_number }}</p>
        <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
    </div>

    <div class="info">
        <p><strong>Ship To:</strong></p>
        <p>{{ $order->address->name ?? '' }}</p>
        <p>{{ $order->address->phone ?? '' }}</p>
        <p>{{ $order->address->address_line1 ?? '' }}</p>
        @if ($order->address->address_line2 ?? false)
            <p>{{ $order->address->address_line2 }}</p>
        @endif
        <p>{{ $order->address->city ?? '' }}, {{ $order->address->state ?? '' }} {{ $order->address->postal_code ?? '' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="check">✓</th>
                <th>Product</th>
                <th>Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td class="check">☐</td>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($order->notes)
        <div class="info" style="margin-top: 20px;">
            <p><strong>Order Notes:</strong> {{ $order->notes }}</p>
        </div>
    @endif

</body>
</html>