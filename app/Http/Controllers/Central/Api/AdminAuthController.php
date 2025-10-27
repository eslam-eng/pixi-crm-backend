<?php

namespace App\Http\Controllers\Central\Api;

use App\DTO\Central\AuthCredentialsDTO;
use App\Helpers\ApiResponse;
use App\Http\Requests\Central\Auth\AuthFormRequest;
use App\Http\Resources\Central\AuthUserResource;
use App\Services\Central\AdminAuthService;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AdminAuthController
{
    public function __invoke(AuthFormRequest $request, AdminAuthService $authService)
    {
        // dd($request->all());
        try {
            $credentials = AuthCredentialsDTO::fromRequest($request);
            $admin = $authService->authenticate($credentials);
            $token = $admin->generateToken();

            $data = [
                'user' => AuthUserResource::make($admin),
                'token' => $token,
            ];

            return ApiResponse::success(data: $data);
        } catch (UnauthorizedHttpException $e) {
            return ApiResponse::unauthorized(__('auth.failed'), []);
        }
    }
}
