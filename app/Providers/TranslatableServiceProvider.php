<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Translatable\Translatable;

class TranslatableServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $translatable = app(Translatable::class);

        // Configure fallback locale
        $translatable->fallback(
            config('translatable.fallback_locale'),
            config('translatable.fallback_any'),
            config('translatable.missing_key_callback')
        );

        // Configure null handling
        $translatable->allowNullForTranslation(
            config('translatable.allow_null_for_translation')
        );

        // Configure empty string handling
        $translatable->allowEmptyStringForTranslation(
            config('translatable.allow_empty_string_for_translation')
        );
    }
} 