<?php

namespace App\Providers;

use App\Models\NilaiSumatifAs;
use App\Observers\NilaiSumatifAsObserver;
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
        NilaiSumatifAs::observe(NilaiSumatifAsObserver::class);
    }
}
