<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Abandoned Cart Reminder Intervals
    |--------------------------------------------------------------------------
    |
    | Define the time intervals for sending abandoned cart reminders.
    | The values are in hours or days after the cart was abandoned.
    |
    */
    'reminder_intervals' => [
        'first_reminder' => env('ABANDONED_CART_FIRST_REMINDER_HOURS', 1),          // 1 hour after abandonment
        'second_reminder' => env('ABANDONED_CART_SECOND_REMINDER_HOURS', 24),      // 24 hours (1 day) after first reminder
        'third_reminder' => env('ABANDONED_CART_THIRD_REMINDER_DAYS', 3),          // 3 days after second reminder
    ],

    /*
    |--------------------------------------------------------------------------
    | Abandoned Cart Threshold
    |--------------------------------------------------------------------------
    |
    | Define how long a cart must be inactive before it's considered abandoned.
    | Value is in minutes.
    |
    */
    'abandoned_threshold_minutes' => env('ABANDONED_CART_THRESHOLD_MINUTES', 60),  // 1 hour

    /*
    |--------------------------------------------------------------------------
    | Discount Settings
    |--------------------------------------------------------------------------
    |
    | Define discount percentages for each reminder level.
    |
    */
    'discount_percentages' => [
        'first_reminder' => env('ABANDONED_CART_FIRST_DISCOUNT', 0),    // No discount for first reminder
        'second_reminder' => env('ABANDONED_CART_SECOND_DISCOUNT', 5),  // 5% for second reminder
        'third_reminder' => env('ABANDONED_CART_THIRD_DISCOUNT', 10),   // 10% for third reminder
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Templates
    |--------------------------------------------------------------------------
    |
    | Define which email templates to use for each reminder.
    |
    */
    'email_templates' => [
        'first_reminder' => 'emails.abandoned-cart-first-reminder',
        'second_reminder' => 'emails.abandoned-cart-second-reminder',
        'third_reminder' => 'emails.abandoned-cart-third-reminder',
    ],
];