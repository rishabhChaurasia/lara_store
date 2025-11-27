<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleBasedSessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the authenticated user
        $user = Auth::user();

        if ($user) {
            // Check if user is an admin
            if ($user->role === 'admin') {
                // Set admin session timeout (from env, default 120 minutes)
                config(['session.lifetime' => env('SESSION_LIFETIME_ADMIN', 120)]);
            } else {
                // Set default user session timeout (from env, default 30 minutes)
                config(['session.lifetime' => env('SESSION_LIFETIME', 30)]);
            }
        }

        return $next($request);
    }
}
