<?php

namespace App\Providers;

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
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Invalidate dashboard cache on data mutation
        $clearCache = function () {
            \Illuminate\Support\Facades\Cache::forget('dashboard_data');
        };

        \App\Models\Order::saved($clearCache);
        \App\Models\Order::deleted($clearCache);
        \App\Models\Customer::saved($clearCache);
        \App\Models\Customer::deleted($clearCache);
        \App\Models\Product::saved($clearCache);
        \App\Models\Product::deleted($clearCache);
        \App\Models\Delivery::saved($clearCache);
        \App\Models\Delivery::deleted($clearCache);
        \App\Models\Invoice::saved($clearCache);
        \App\Models\Invoice::deleted($clearCache);
        \App\Models\Payment::saved($clearCache);
        \App\Models\Payment::deleted($clearCache);
        \App\Models\ActivityLog::saved($clearCache);
        \App\Models\ActivityLog::deleted($clearCache);
    }
}
