<?php

namespace App\Services;

use App\Models\AbandonedCartSetting;

class AbandonedCartConfigService
{
    /**
     * Get a configuration value, checking database first then config file
     */
    public static function get($key, $default = null)
    {
        // First try to get from database
        $dbValue = AbandonedCartSetting::getSetting($key);
        
        if ($dbValue !== null) {
            return $dbValue;
        }
        
        // Fall back to config file
        return config("abandoned_cart.{$key}", $default);
    }

    /**
     * Set a configuration value in the database
     */
    public static function set($key, $value): void
    {
        AbandonedCartSetting::setSetting($key, $value);
    }

    /**
     * Get all abandoned cart settings
     */
    public static function getAllSettings(): array
    {
        return [
            'first_reminder_hours' => self::get('first_reminder_hours', 1),
            'second_reminder_hours' => self::get('second_reminder_hours', 24),
            'third_reminder_days' => self::get('third_reminder_days', 3),
            'abandoned_threshold_minutes' => self::get('abandoned_threshold_minutes', 60),
            'second_reminder_discount' => self::get('second_reminder_discount', 5),
            'third_reminder_discount' => self::get('third_reminder_discount', 10),
        ];
    }
}