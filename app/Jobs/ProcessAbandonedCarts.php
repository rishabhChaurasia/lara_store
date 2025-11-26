<?php

namespace App\Jobs;

use App\Models\Cart;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessAbandonedCarts implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Find all carts that are marked as abandoned and need a reminder
        // Performance note: This query is optimized with proper database indexes on:
        // - abandoned_at
        // - remind_at
        // - notified_count
        $carts = Cart::whereNotNull('abandoned_at')
                     ->whereNotNull('remind_at')
                     ->where('remind_at', '<=', now())
                     ->where('notified_count', '<', 3) // Only send up to 3 reminders
                     ->get();

        foreach ($carts as $cart) {
            // Check if there are items in the cart before sending notification
            if ($cart->items()->count() > 0) {
                // Dispatch job to send the abandoned cart notification
                dispatch(new SendAbandonedCartNotification($cart));
            }

            // Increment notification count and schedule next reminder
            $cart->incrementNotificationCount();
            $cart->scheduleNextReminder();
        }

        Log::info('Processed ' . $carts->count() . ' abandoned carts');
    }
}
