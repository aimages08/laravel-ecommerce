<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family: Arial, sans-serif; background:#f3f4f6; padding:20px;">
    <div style="max-width:600px; margin:auto; background:#fff; border-radius:8px; padding:30px;">

        <h1 style="color:#4f46e5; margin:0 0 10px;">
            {{ setting('store_name', 'Shoping Website') }}
        </h1>

        <p>Hi <strong>{{ $order->address->name ?? $order->user->name ?? 'Customer' }}</strong>,</p>

        <p>Thank you for your order <strong>#{{ $order->order_number }}</strong>.</p>

        <p>Please transfer <strong>Rs. {{ number_format($order->total) }}</strong> to the following account:</p>

        <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:6px; padding:20px; margin:20px 0;">
            <table style="width:100%; border-collapse:collapse;">
                @foreach (['bank_name','account_title','account_number','iban'] as $key)
                    @if ($method->getSetting($key))
                        <tr>
                            <td style="padding:6px 0; color:#6b7280; text-transform:capitalize;">
                                {{ str_replace('_', ' ', $key) }}:
                            </td>
                            <td style="padding:6px 0; font-weight:600;">
                                {{ $method->getSetting($key) }}
                            </td>
                        </tr>
                    @endif
                @endforeach
            </table>
        </div>

        @if ($method->instructions)
            <p style="color:#374151;">{{ $method->instructions }}</p>
        @endif

        <p>Please send us the payment receipt once transferred so we can process your order quickly.</p>

        <p style="margin-top:30px; font-size:12px; color:#888;">
            Thank you for shopping with us!
        </p>
    </div>
</body>
</html>