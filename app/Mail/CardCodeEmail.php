<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CardCodeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        $order->load('items.card', 'user');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Card Code',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.card-code',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
