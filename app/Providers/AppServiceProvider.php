<?php

namespace App\Providers;

use App\Models\TransactionCategory;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
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
        Paginator::useTailwind();
        
        Blade::if('hasservice', function (string $serviceSlug) {
            $currentApp = auth()->user()->app;
            return $currentApp && $currentApp->hasService($serviceSlug);
        });
    }
}
