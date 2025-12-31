<?php

namespace App\Http\Middleware;

use App\Models\Central\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckZapierApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $zapier_api_key = $request->header('X-API-KEY') ?? $request->query('api_key');

        if (!$zapier_api_key) {
            return response()->json(['message' => 'API Key missing'], 401);
        }

        // Search in the data JSON column
        $tenant = Tenant::where('zapier_api_key', $zapier_api_key)->first();

        if (!$tenant) {
            return response()->json(['message' => 'Invalid API Key'], 401);
        }

        // Initialize Tenancy
        tenancy()->initialize($tenant);

        // Attempt to login the first admin user of the tenant (for Zapier to have a user context)
        // Adjust this logic if you have a specific user to impersonate
        $adminInfo = null;

        // Try to find a user to authenticate as, to allow perform actions
        // This assumes App\Models\Tenant\User exists and we want to login as the 'Owner' or first user
        // Note: Central Tenant 'owner_id' might point to Central User, so we look inside Tenant User table
        $adminUser = \App\Models\Tenant\User::first(); # Simple fallback

        if ($adminUser) {
            // Assuming 'web' or 'api' guard is used for Tenant Users
            \Illuminate\Support\Facades\Auth::guard('web')->login($adminUser);
        }

        // Store tenant in request for Controller access if needed
        $request->merge(['current_tenant' => $tenant]);

        return $next($request);
    }
}
