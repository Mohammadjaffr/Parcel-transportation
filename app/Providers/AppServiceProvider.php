<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\TransactionCategory;

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
        
        // Share transaction categories with app layout for global transaction modal
        view()->composer('layouts.app', function ($view) {
            $categories = TransactionCategory::where('is_active', true)
                ->orderBy('name')
                ->get();
            $view->with('categories', $categories);
        });
    }
}
