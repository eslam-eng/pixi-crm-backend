<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SkipTenantParameter
{
    public function handle(Request $request, Closure $next)
    {
        // Store the tenant for later use
        $request->attributes->set('tenant', $request->route('tenant'));

        // Get the route instance
        $route = $request->route();

        // Remove 'tenant' from the route parameters
        $parameters = collect($route->parameters())->except('tenant');

        // Set the filtered parameters back on the route (MUST be associative)
        $route->setParameter('tenant', null); // optional: nullify instead of removing

        foreach ($parameters as $key => $value) {
            $route->setParameter($key, $value); // set each parameter back
        }

        return $next($request);
    }
}
