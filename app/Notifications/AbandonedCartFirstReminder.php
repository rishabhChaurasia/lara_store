<?php

namespace App\Notifications;

use App\Mail\AbandonedCartFirstReminderMail;
use App\Models\AbandonedCartNotification as AbandonedCartNotificationModel;
use App\Models\Cart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class AbandonedCartFirstReminder extends Notification
{
    use Queueable;

    public Cart $cart;

    public AbandonedCartNotificationModel $notification;

    /**
     * Create a new notification instance.
     */
    public function __construct(Cart $cart, AbandonedCartNotificationModel $notification)
    {
        $this->cart = $cart;
        $this->notification = $notification;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable)
    {
        return (new AbandonedCartFirstReminderMail($this->cart));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'cart_id' => $this->cart->id,
            'notification_id' => $this->notification->id,
            'type' => 'first_reminder',
        ];
    }
}
