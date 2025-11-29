<?php

namespace App\Http\Middleware;

use App\Models\Settings\Tenant\RegionalSettings;
use Closure;
use Illuminate\Http\Request;

class SetUserTimezone
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user) {
            // Set the user's timezone
            $timezone = app(RegionalSettings::class)?->timezone;
            date_default_timezone_set($timezone ?? config('app.timezone'));
        }

        return $next($request);
    }
}
