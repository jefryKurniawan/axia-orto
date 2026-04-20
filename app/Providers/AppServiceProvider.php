<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        // Define gates for role-based access
        Gate::define('access-consultations', function ($user) {
            return in_array($user->role, ['admin', 'dokter']);
        });

        Gate::define('access-inventory', function ($user) {
            return in_array($user->role, ['admin', 'staf_klinik']);
        });

        Gate::define('access-reports', function ($user) {
            return $user->role === 'admin';
        });

        // Gate untuk spesifik role
        Gate::define('isAdmin', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('isDokter', function ($user) {
            return $user->role === 'dokter';
        });

        Gate::define('isStaf', function ($user) {
            return $user->role === 'staf_klinik';
        });
    }
}
