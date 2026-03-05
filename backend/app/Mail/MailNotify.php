<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailNotify extends Mailable
{
    use Queueable, SerializesModels;

    public $cart;
    public $total;
    public $user;

    public function __construct($cart, $total, $user)
    {
        $this->cart  = $cart;
        $this->total = $total;
        $this->user  = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác nhận đơn hàng'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order',
            with: [
                'cart'  => $this->cart,
                'total' => $this->total,
                'user'  => $this->user,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
