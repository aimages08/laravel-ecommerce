<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Confirmed</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f3f4f6; padding:20px;">
    <div style="max-width:600px; margin:auto; background:#fff; border-radius:8px; padding:30px;">

        <h1 style="color:#4f46e5; margin:0 0 10px;">
            {{ setting('store_name', 'Shoping Website') }}
        </h1>
        <p>Hi <strong>{{ $order->address->name ?? $order->user->name ?? 'Customer' }}</strong>,</p>

        <p>Thank you for your order! Here are the details:</p>

        <table style="width:100%; border-collapse:collapse; margin:15px 0;">
            <tr>
                <td style="padding:6px 0;"><strong>Order #:</strong></td>
                <td>{{ $order->order_number }}</td>
            </tr>
            <tr>
                <td style="padding:6px 0;"><strong>Status:</strong></td>
                <td>{{ ucfirst($order->status) }}</td>
            </tr>
            <tr>
                <td style="padding:6px 0;"><strong>Payment:</strong></td>
                <td>{{ strtoupper($order->payment_method) }} — {{ ucfirst($order->payment_status) }}</td>
            </tr>
        </table>

        <h3 style="border-bottom:1px solid #eee; padding-bottom:6px;">Items</h3>
        <table style="width:100%; border-collapse:collapse;">
            @foreach ($order->items as $item)
                <tr>
                    <td style="padding:6px 0;">
                        {{ $item->product_name }} × {{ $item->quantity }}
                    </td>
                    <td style="text-align:right;">
                        Rs. {{ number_format($item->subtotal) }}
                    </td>
                </tr>
            @endforeach
        </table>

        <div style="margin-top:15px; border-top:1px solid #eee; padding-top:10px;">
            <p style="margin:4px 0;">Subtotal: Rs. {{ number_format($order->subtotal) }}</p>
            @if ($order->discount > 0)
                <p style="margin:4px 0; color:green;">
                    Discount: - Rs. {{ number_format($order->discount) }}
                </p>
            @endif
            <p style="margin:4px 0;">Shipping: Rs. {{ number_format($order->shipping) }}</p>
            <p style="margin:8px 0; font-size:18px;"><strong>Total: Rs. {{ number_format($order->total) }}</strong></p>
        </div>

        <p style="margin-top:20px;">
            <a href="{{ route('shop.order.detail', $order->order_number) }}"
               style="background:#4f46e5; color:#fff; padding:10px 20px; text-decoration:none; border-radius:5px;">
                View Order
            </a>
        </p>

        <p style="margin-top:30px; font-size:12px; color:#888;">
            Thank you for shopping with us!
        </p>
    </div>
</body>
</html>