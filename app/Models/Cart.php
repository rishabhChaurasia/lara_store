<?php

namespace App\Models;

use App\Services\AbandonedCartConfigService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'expires_at',
        'abandoned_at',
        'remind_at',
        'notified_count',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'abandoned_at' => 'datetime',
        'remind_at' => 'datetime',
        'notified_count' => 'integer',
    ];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notifications()
    {
        return $this->hasMany(AbandonedCartNotification::class);
    }

    /**
     * Mark the cart as abandoned
     */
    public function markAsAbandoned()
    {
        $firstReminderHours = AbandonedCartConfigService::get('first_reminder_hours', 1);

        $this->update([
            'abandoned_at' => Carbon::now(),
            'remind_at' => Carbon::now()->addHours($firstReminderHours), // First reminder after configured hours
        ]);
    }

    /**
     * Check if the cart is abandoned (not updated in more than configured threshold)
     */
    public function isAbandoned(): bool
    {
        if ($this->abandoned_at) {
            return true;
        }

        // Check if last activity was more than threshold minutes ago
        $thresholdMinutes = AbandonedCartConfigService::get('abandoned_threshold_minutes', 60);
        $lastActivity = $this->updated_at ?? $this->created_at;
        return $lastActivity->lt(Carbon::now()->subMinutes($thresholdMinutes));
    }

    /**
     * Check if it's time to send a reminder
     */
    public function isTimeForReminder(): bool
    {
        return $this->remind_at && $this->remind_at->lte(Carbon::now());
    }

    /**
     * Get the next notification type based on notified count
     */
    public function getNextNotificationType(): ?string
    {
        if ($this->notified_count >= 3) {
            return null; // Already sent all reminders
        }

        return match ($this->notified_count) {
            0 => 'first_reminder',
            1 => 'second_reminder',
            2 => 'third_reminder',
            default => null,
        };
    }

    /**
     * Schedule next reminder based on notification count
     */
    public function scheduleNextReminder(): void
    {
        $nextReminderTime = match ($this->notified_count) {
            0 => Carbon::now()->addHours(AbandonedCartConfigService::get('second_reminder_hours', 24)),      // Hours after first reminder
            1 => Carbon::now()->addDays(AbandonedCartConfigService::get('third_reminder_days', 3)),        // Days after second reminder
            default => null,
        };

        if ($nextReminderTime) {
            $this->update(['remind_at' => $nextReminderTime]);
        } else {
            $this->update(['remind_at' => null]); // No more reminders
        }
    }

    /**
     * Increment the notified count
     */
    public function incrementNotificationCount(): void
    {
        $this->update([
            'notified_count' => $this->notified_count + 1,
        ]);
    }
}
