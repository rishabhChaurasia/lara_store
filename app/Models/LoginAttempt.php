<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoginAttempt extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'ip_address',
        'user_agent',
        'success',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'success' => 'boolean',
        'attempted_at' => 'datetime',
    ];

    /**
     * Scope for getting failed attempts for an email within a time period.
     */
    public function scopeFailedForEmail($query, string $email, int $minutes = 15)
    {
        return $query->where('email', $email)
                    ->where('success', false)
                    ->where('attempted_at', '>=', now()->subMinutes($minutes));
    }

    /**
     * Scope for getting failed attempts for an IP within a time period.
     */
    public function scopeFailedForIp($query, string $ip, int $minutes = 15)
    {
        return $query->where('ip_address', $ip)
                    ->where('success', false)
                    ->where('attempted_at', '>=', now()->subMinutes($minutes));
    }

    /**
     * Get count of failed attempts for an email in the last specified minutes.
     */
    public static function countFailedForEmail(string $email, int $minutes = 15): int
    {
        return static::failedForEmail($email, $minutes)->count();
    }

    /**
     * Get count of failed attempts for an IP in the last specified minutes.
     */
    public static function countFailedForIp(string $ip, int $minutes = 15): int
    {
        return static::failedForIp($ip, $minutes)->count();
    }

    /**
     * Record a login attempt and check for account lockout.
     */
    public static function record(string $email, string $ip, string $userAgent, bool $success): ?bool
    {
        $attempt = static::create([
            'email' => $email,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'success' => $success,
        ]);

        // If the login failed, check if the user should be locked out
        if (!$success) {
            if (static::shouldLockAccount($email)) {
                // Lock the user account
                static::lockAccount($email);
                return true; // Indicates account was locked
            }
        }

        return null; // Indicates no special action taken
    }

    /**
     * Check if an account should be locked based on failed attempts.
     */
    public static function shouldLockAccount(string $email): bool
    {
        // Check if there are 5 or more failed attempts in the last 15 minutes
        $failedAttempts = static::failedForEmail($email, 15)->count();
        return $failedAttempts >= 5;
    }

    /**
     * Lock an account by suspending the user.
     */
    public static function lockAccount(string $email): void
    {
        $user = \App\Models\User::where('email', $email)->first();

        if ($user) {
            // Lock account for 30 minutes
            $lockoutUntil = now()->addMinutes(30);

            $user->update([
                'suspended_at' => $lockoutUntil,
                'suspension_reason' => 'Account locked due to multiple failed login attempts',
            ]);
        }
    }

    /**
     * Check if an account is currently locked.
     */
    public static function isAccountLocked(string $email): bool
    {
        $user = \App\Models\User::where('email', $email)->first();

        if (!$user) {
            return false;
        }

        // Check if suspended and suspension is still active (not expired)
        // suspended_at contains the time until which the account is suspended
        return $user->suspended_at && $user->suspended_at->isAfter(now());
    }

    /**
     * Unlock an account.
     */
    public static function unlockAccount(string $email): void
    {
        $user = \App\Models\User::where('email', $email)->first();

        if ($user && $user->suspended_at) {
            $user->update([
                'suspended_at' => null,
                'suspension_reason' => null,
            ]);
        }
    }
}