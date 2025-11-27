<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckPasswordExpiration;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\IsAdmin; // Import the middleware
use App\Http\Middleware\RoleBasedSessionTimeout; // Import the session timeout middleware
use App\Providers\EventServiceProvider; // Import the event service provider
use App\Console\Kernel; // Import the Console Kernel

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: Kernel::class, // Use the Console Kernel for commands
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append([
            RoleBasedSessionTimeout::class,
            CheckPasswordExpiration::class,
        ]);

        $middleware->alias([
            'admin' => IsAdmin::class, // Register the middleware alias
            'permission' => CheckPermission::class, // Register the permission middleware
        ]);
    })
    ->withProviders([
        EventServiceProvider::class,
    ])
    ->withEvents()
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();