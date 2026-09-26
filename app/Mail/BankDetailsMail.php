<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BankDetailsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order, public PaymentMethod $method) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bank Details for Order ' . $this->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bank-details',
        );
    }
}