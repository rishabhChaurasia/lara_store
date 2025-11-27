<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPasswordExpiration
{
    /**
     * Maximum days before password expires
     */
    const MAX_PASSWORD_AGE_DAYS = 90;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // Only check for password expiration if password_changed_at is set
            if ($user->password_changed_at) {
                $daysSincePasswordChange = $user->password_changed_at->diffInDays(now());

                // If password is older than the maximum age, redirect to change password
                if ($daysSincePasswordChange >= self::MAX_PASSWORD_AGE_DAYS) {
                    // Allow access to profile and password update routes, but redirect elsewhere
                    if (!$this->isPasswordChangeRoute($request)) {
                        return redirect()->route('profile.edit')
                            ->with('status', 'password-expired')
                            ->with('message', 'Your password has expired. Please update your password.');
                    }
                }
            }
        }

        return $next($request);
    }

    /**
     * Check if the current route is for changing password or viewing profile
     */
    private function isPasswordChangeRoute(Request $request): bool
    {
        return $request->route()->getName() === 'profile.edit' ||
               $request->route()->getName() === 'password.update' ||
               $request->is('profile*') ||
               $request->is('password*');
    }
}
