<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Order;
use App\Observers\OrderObserver;
use App\Policies\CartPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Order::observe(OrderObserver::class);
    }

    /**
     * Register the application's policies.
     */
    private function registerPolicies(): void
    {
        Gate::policy(Cart::class, CartPolicy::class);
    }
}
