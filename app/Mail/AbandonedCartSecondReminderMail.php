<?php

namespace App\Mail;

use App\Models\Cart;
use App\Services\AbandonedCartConfigService;
use App\Services\AbandonedCartDiscountService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AbandonedCartSecondReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public Cart $cart;

    public $discountCode;

    /**
     * Create a new message instance.
     */
    public function __construct(Cart $cart)
    {
        $this->cart = $cart;

        // Generate discount for second reminder
        $discountService = new AbandonedCartDiscountService();
        $coupon = $discountService->generateDiscountForReminder(2, $cart->id);
        $this->discountCode = $coupon ? $coupon->code : null;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = AbandonedCartConfigService::get('second_reminder_subject', 'Your items are still waiting - Complete your purchase!');

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.abandoned-cart-second-reminder',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
