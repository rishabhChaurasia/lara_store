<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\LoginAttempt;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Check if the account is locked before processing
        if (LoginAttempt::isAccountLocked($this->email)) {
            throw ValidationException::withMessages([
                'email' => 'Your account is temporarily locked due to multiple failed login attempts. Please try again later.',
            ]);
        }

        // Record the login attempt
        $lockoutStatus = LoginAttempt::record(
            $this->email,
            $this->ip(),
            $this->userAgent(),
            false // will be updated to true if successful
        );

        // If account was just locked due to this attempt, deny access
        if ($lockoutStatus === true) {
            throw ValidationException::withMessages([
                'email' => 'Your account has been temporarily locked due to multiple failed login attempts. Please try again later.',
            ]);
        }

        $user = User::where('email', $this->email)->first();

        if (! $user || ! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Update the login attempt to successful
        LoginAttempt::where('email', $this->email)
            ->where('ip_address', $this->ip())
            ->latest()
            ->first()
            ?->update(['success' => true]);

        // Check if user has two-factor authentication enabled
        // Require 2FA for all users who have it enabled
        if ($user->two_factor_enabled && $user->hasEnabledTwoFactor()) {
            // Store the user in the session for the 2FA verification
            request()->session()->put('login.user', $user);

            // Logout the user temporarily
            Auth::logout();

            // Redirect to 2FA challenge by setting a session flag
            request()->session()->put('auth.2fa_required', true);

            // Throw validation exception to redirect to the 2FA page
            throw ValidationException::withMessages([
                'email' => trans('auth.two_factor_required'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        // Check our custom login attempts in addition to Laravel's rate limiter
        $attemptsForEmail = LoginAttempt::countFailedForEmail($this->email, 15);
        $attemptsForIp = LoginAttempt::countFailedForIp($this->ip(), 15);

        // If too many failed attempts (e.g., 5 in 15 minutes) for email or IP
        if ($attemptsForEmail >= 5 || $attemptsForIp >= 5) {
            $lockoutTime = 30 * 60; // 30 minutes lockout

            // Add additional throttling to prevent further attempts
            RateLimiter::hit($this->throttleKey(), $lockoutTime);

            event(new Lockout($this));

            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $lockoutTime,
                    'minutes' => ceil($lockoutTime / 60),
                ]),
            ]);
        }

        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
