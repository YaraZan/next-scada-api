<?php

namespace App\Providers;

use App\Policies\MemberRolePolicy;
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
        /** Member roles */
        Gate::define('viewFromWorkspace-memberRole', [MemberRolePolicy::class, 'viewFromWorkspace']);
    }
}
