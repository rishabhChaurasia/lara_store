<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\IsAdmin; // Import the middleware
use App\Providers\EventServiceProvider; // Import the event service provider
use App\Console\Kernel; // Import the Console Kernel

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: Kernel::class, // Use the Console Kernel for commands
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => IsAdmin::class, // Register the middleware alias
        ]);
    })
    ->withProviders([
        EventServiceProvider::class,
    ])
    ->withEvents()
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
