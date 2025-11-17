<?php

namespace App\Http\Controllers\Central\Api\Auth;

use App\DTO\Central\AuthCredentialsDTO;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Auth\AuthFormRequest;
use App\Http\Resources\Central\AuthUserResource;
use App\Services\Central\AuthService;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthController extends Controller
{
    public function __invoke(AuthFormRequest $request, AuthService $authService)
    {
        try {
            $credentials = AuthCredentialsDTO::fromRequest($request);
            $user = $authService->authenticate($credentials);

            $tenant = $user->tenant;
            if (! $tenant || ! $tenant->status) {
                return response()->json(['error' => 'Tenant not active'], 403);
            }

            // $tenant->makeCurrent();

            // Create tenant-specific token
            $token = $user->generateToken(name: 'multi-tenant-access', abilities: ['tenant:'.$tenant->id]);

            $data = [
                'token' => $token,
                'user' => AuthUserResource::make($user),
            ];

            return ApiResponse::success(data: $data);
        } catch (UnauthorizedHttpException $e) {
            return ApiResponse::unauthorized(__('auth.failed'), []);
        }
    }
}
