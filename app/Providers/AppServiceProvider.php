<?php

namespace App\Providers;

use App\Models\Kelas;
use App\Models\NilaiMapel;
use App\Models\NilaiSumatifAs;
use App\Models\Siswa;
use App\Models\User;
use App\Observers\NilaiSumatifAsObserver;
use App\Policies\KelasPolicy;
use App\Policies\NilaiMapelPolicy;
use App\Policies\SiswaPolicy;
use App\Policies\UserPolicy;
use App\View\Composers\SekolahComposer;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
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

        View::composer('*', SekolahComposer::class);

        Gate::policy(Siswa::class, SiswaPolicy::class);
        Gate::policy(Kelas::class, KelasPolicy::class);
        Gate::policy(NilaiMapel::class, NilaiMapelPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }
}
