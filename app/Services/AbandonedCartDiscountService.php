<?php

namespace App\Services;

use App\Models\Coupon;
use Carbon\Carbon;

class AbandonedCartDiscountService
{
    /**
     * Generate a discount code for abandoned cart reminders
     *
     * @param int $reminderNumber The reminder number (1, 2, or 3)
     * @param int $cartId The cart ID for context
     * @return Coupon|null
     */
    public function generateDiscountForReminder(int $reminderNumber, int $cartId): ?Coupon
    {
        // Define discount percentages based on reminder number and configuration
        $discountPercentages = [
            1 => 0,    // No discount for first reminder
            2 => (int) \App\Services\AbandonedCartConfigService::get('second_reminder_discount', 5),  // Configurable discount for second reminder
            3 => (int) \App\Services\AbandonedCartConfigService::get('third_reminder_discount', 10),  // Configurable discount for third reminder
        ];

        $discountPercentage = $discountPercentages[$reminderNumber] ?? 0;

        // Don't create a coupon if no discount
        if ($discountPercentage <= 0) {
            return null;
        }

        // Generate a unique coupon code
        $code = "ABANDONED_{$cartId}_" . strtoupper(uniqid());

        // Create the coupon
        return Coupon::create([
            'code' => $code,
            'type' => 'percentage',
            'value' => $discountPercentage,
            'min_amount' => 0,
            'usage_limit' => 1, // One-time use
            'usage_count' => 0,
            'expires_at' => Carbon::now()->addDays(7), // Valid for 7 days
            'is_active' => true,
        ]);
    }
}