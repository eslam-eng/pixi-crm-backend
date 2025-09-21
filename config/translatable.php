<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default locale
    |--------------------------------------------------------------------------
    |
    | This is the default locale when translating a model
    |
    */
    'locale' => env('APP_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Fallback locale
    |--------------------------------------------------------------------------
    |
    | This is the fallback locale when translating a model
    |
    */
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Available locales
    |--------------------------------------------------------------------------
    |
    | This is the list of available locales for your application
    |
    */
    'available_locales' => [
        'en' => 'English',
        'ar' => 'العربية',
    ],

    /*
    |--------------------------------------------------------------------------
    | Allow null for translation
    |--------------------------------------------------------------------------
    |
    | If a translation has not been set for a given locale, return null instead of empty string
    |
    */
    'allow_null_for_translation' => false,

    /*
    |--------------------------------------------------------------------------
    | Allow empty string for translation
    |--------------------------------------------------------------------------
    |
    | If a translation has not been set for a given locale, return empty string instead of null
    |
    */
    'allow_empty_string_for_translation' => true,

    /*
    |--------------------------------------------------------------------------
    | Fallback any locale
    |--------------------------------------------------------------------------
    |
    | If a translation has not been set for a given locale and the fallback locale,
    | any other locale will be chosen instead
    |
    */
    'fallback_any' => false,

    /*
    |--------------------------------------------------------------------------
    | Missing key callback
    |--------------------------------------------------------------------------
    |
    | Callback function to handle missing translation keys
    | Set to null to disable
    |
    */
    'missing_key_callback' => null,
]; 