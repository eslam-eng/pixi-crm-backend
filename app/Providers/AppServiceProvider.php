<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

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
        Relation::enforceMorphMap([
            'product'  => \App\Models\Tenant\Product::class,
            'service' => \App\Models\Tenant\Service::class,
            'user'    => \App\Models\Tenant\User::class,
        ]);
    }
}
