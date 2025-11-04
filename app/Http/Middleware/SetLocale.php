<?php

namespace App\Http\Middleware;

use App\Enum\SupportedLocalesEnum;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated and has a locale
        $locale = auth()->check() ? auth()->user()->locale : null;

        // Fallbacks
        if (! $locale && session()->has('locale')) {
            $locale = session('locale');
        }
        if (! $locale && $request->hasHeader('Accept-Language')) {
            $locale = substr($request->getPreferredLanguage(SupportedLocalesEnum::values()), 0, 2);
        }
        if (! $locale) {
            $locale = config('app.locale', 'en');
        }

        // Validate locale
        if (! in_array($locale, SupportedLocalesEnum::values())) {
            $locale = config('app.locale', 'en');
        }

        App::setLocale($locale);
        // For Carbon date localization
        Carbon::setLocale($locale);
        session(['locale' => $locale]);

        return $next($request);
    }
}
