<?php

namespace App\Jobs;

use App\Models\AbandonedCartNotification as AbandonedCartNotificationModel;
use App\Models\Cart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendAbandonedCartNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Cart $cart;

    /**
     * Create a new job instance.
     */
    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get the next notification type
        $notificationType = $this->cart->getNextNotificationType();

        if (!$notificationType) {
            Log::info('No more notifications to send for cart ID: ' . $this->cart->id);
            return;
        }

        // Get the user's email
        $user = $this->cart->user;
        if (!$user || !$user->email) {
            Log::warning('User email not available for cart ID: ' . $this->cart->id);
            return;
        }

        // Create notification record
        $notification = AbandonedCartNotificationModel::create([
            'cart_id' => $this->cart->id,
            'user_id' => $user->id,
            'notification_type' => $notificationType,
            'sent_at' => now(),
        ]);

        // Send email notification
        Mail::to($user->email)->send(
            match ($notificationType) {
                'first_reminder' => new \App\Notifications\AbandonedCartFirstReminder($this->cart, $notification),
                'second_reminder' => new \App\Notifications\AbandonedCartSecondReminder($this->cart, $notification),
                'third_reminder' => new \App\Notifications\AbandonedCartThirdReminder($this->cart, $notification),
                default => new \App\Notifications\AbandonedCartFirstReminder($this->cart, $notification),
            }
        );

        Log::info("Abandoned cart {$notificationType} sent to user: {$user->email} for cart ID: {$this->cart->id}");
    }
}
