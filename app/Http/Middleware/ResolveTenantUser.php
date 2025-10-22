<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use App\Models\Landlord\Tenant;
use App\Models\Tenant\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenantUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    //    public function handle(Request $request, Closure $next): Response
    //    {
    //        // Only proceed if a tenant is set and a user is authenticated && auth user is instance for Landlord user
    //
    //        if (Tenant::current() && auth()->check()) {
    //            $landlordUser = auth()->user(); // Landlord user
    //
    //            $tenant = Tenant::current();
    //
    //            // Switch to tenant database (handled by SwitchTenantDatabaseTask)
    //            $tenant->makeCurrent();
    //
    //            // Find tenant-specific user by email
    //            $tenantUser = User::where('email', $landlordUser->email)->first();
    //
    //            if ($tenantUser) {
    //                // Set tenant user as the authenticated user
    //                Auth::setUser($tenantUser);
    //            } else {
    //                return ApiResponse::unauthorized(message: 'Tenant user not authorized to access ');
    //            }
    //        }
    //
    //        return $next($request);
    //
    //    }

    public function handle(Request $request, Closure $next): Response
    {
        // Only proceed if a tenant is set and a user is authenticated
        if (Tenant::current() && auth()->check()) {
            $landlordUser = auth()->user();
            $tenant = Tenant::current();

            // Create cache key based on tenant and user
            $cacheKey = "tenant_user:{$tenant->id}:{$landlordUser->id}";

            // Try to get cached tenant user data
            $cachedTenantUserData = Cache::rememberForever($cacheKey, function () use ($tenant, $landlordUser) {
                // Switch to tenant database
                $tenant->makeCurrent();

                // Find tenant-specific user by email
                $tenantUser = User::where('email', $landlordUser->email)
                    ->select(['id', 'email', 'name', 'created_at', 'updated_at']) // Only select needed fields
                    ->first();

                return $tenantUser;
            });

            if ($cachedTenantUserData) {
                // Set tenant user as the authenticated user
                Auth::setUser($cachedTenantUserData);
            } else {
                // Clear cache for this user if not found
                Cache::forget($cacheKey);

                return ApiResponse::unauthorized(message: 'Tenant user not authorized to access');
            }
        }

        return $next($request);
    }
}
