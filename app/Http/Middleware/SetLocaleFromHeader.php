<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from Accept-Language header or custom X-Locale header
        $locale = $request->header('X-Locale') ?? 
                  $request->header('Accept-Language') ?? 
                  config('app.locale', 'en');
        
        // If Accept-Language header, extract the first language code
        if ($request->header('Accept-Language') && !$request->header('X-Locale')) {
            $locale = substr($locale, 0, 2);
        }
        
        // Validate locale (you can add more supported locales here)
        $supportedLocales = ['en', 'ar','es','fr']; // Add more as needed
        if (!in_array($locale, $supportedLocales)) {
            $locale = config('app.fallback_locale', 'en'); // Use config fallback
        }
        
        // Set the application locale
        app()->setLocale($locale);
        
        return $next($request);
    }
} 