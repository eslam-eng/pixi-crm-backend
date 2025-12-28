<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant\Integration;
use App\Enums\PlatformEnum;

class ZapierAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-KEY');

        if (!$apiKey) {
            return response()->json(['message' => 'Missing API Key', 'success' => false], 401);
        }

        $integration = Integration::where('platform', PlatformEnum::ZAPIER)
            ->first();

        if (!$integration || !isset($integration->settings['api_key']) || $integration->settings['api_key'] !== $apiKey) {
            return response()->json(['message' => 'Invalid API Key', 'success' => false], 401);
        }

        return $next($request);
    }
}
