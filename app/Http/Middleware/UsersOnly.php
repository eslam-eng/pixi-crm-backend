<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UsersOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Reject if authenticated entity is an Admin
        if ($request->user() instanceof Admin) {
            return ApiResponse::forbidden(message: 'Admins are not allowed to access this route.');
        }

        return $next($request);
    }
}
