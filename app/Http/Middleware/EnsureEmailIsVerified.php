<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use Closure;
use Illuminate\Http\Request;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user()->fresh();
        if (! $user || ! $user->email_verified_at) {
            return ApiResponse::error(
                message: 'Email not verified. Please verify your email to access this resource.',
                code: 403
            );
        }

        return $next($request);
    }
}
