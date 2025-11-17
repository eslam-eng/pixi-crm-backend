<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestorePostMethod
{
    /**
     * Handle an incoming request.
     * 
     * This middleware restores POST method when it's been converted to GET
     * due to HTTP redirects (which is standard HTTP behavior).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if this is a GET request but should be POST
        // This happens when POST requests go through HTTP redirects
        
        // Method 1: Check for X-Http-Method-Override header
        if ($request->header('X-Http-Method-Override')) {
            $method = strtoupper($request->header('X-Http-Method-Override'));
            if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                $request->setMethod($method);
            }
        }
        
        // Method 2: Check for _method parameter (Laravel's method spoofing)
        if ($request->has('_method')) {
            $method = strtoupper($request->input('_method'));
            if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                $request->setMethod($method);
            }
        }
        
        // Method 3: If it's a GET request but has POST-like characteristics
        // (Content-Type header, request body, etc.), try to restore POST
        if ($request->isMethod('GET') && 
            ($request->header('Content-Type') === 'application/json' || 
             $request->header('Content-Type') === 'application/x-www-form-urlencoded') &&
            $request->getContent()) {
            // This is likely a POST that was converted to GET
            // Log it for debugging
            \Log::warning('POST request converted to GET detected', [
                'url' => $request->url(),
                'headers' => $request->headers->all(),
            ]);
        }
        
        return $next($request);
    }
}

