<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $currentTenant = Tenant::current();

        if (! $user || ! $currentTenant) {
            return ApiResponse::unauthorized(message: 'Unauthorized tenant access No Tenant Provided in url');
        }

        // Check if the user's token scope matches the current tenant
        $token = $user->currentAccessToken();
        if (! $token->can('tenant:'.$currentTenant->id)) {
            return ApiResponse::unauthorized(message: 'Unauthorized tenant access');
        }

        return $next($request);
    }
}
