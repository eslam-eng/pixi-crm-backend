<?php

namespace App\Http\Middleware;

use App\Http\Resources\Tenant\Users\UserResource;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Check if request expects JSON (API request)
                if ($request->expectsJson() || $request->is('api/*')) {
                    $user = Auth::guard($guard)->user();
                    return apiResponse([
                        'token' => $request->bearerToken(),
                        'token_type' => 'Bearer',
                        'user' => new UserResource($user),
                        'roles' => $user->getRoleNames(),
                        'permissions' => $user->getAllPermissions()->pluck('name'),
                    ], trans('app.You are already authenticated'), 200);
                }

                // For web requests, redirect to dashboard or home
                return redirect('/dashboard'); // or wherever you want to redirect web users
            }
        }

        return $next($request);
    }
}
