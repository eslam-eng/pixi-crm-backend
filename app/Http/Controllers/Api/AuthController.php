<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SignupRequest;
use App\Http\Resources\Tenant\Users\UserResource;
use App\Services\AuthService;
use App\Services\Tenant\Users\UserService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService, private UserService $userService) {}

    /**
     * Handle user signup.
     */
    public function signup(SignupRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $user = $this->authService->signup(
                first_name: $request->first_name,
                last_name: $request->last_name,
                email: $request->email,
                password: $request->password,
                type: $request->type
            );
            DB::commit();
            return apiResponse(new UserResource($user), trans('app.signup_successfully'), 201);
        } catch (Exception $e) {
            DB::rollBack();
            return apiResponse(null, $e->getMessage(), 500);
        }
    }

    /**
     * Handle user login.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            // Authenticate user
            $user = $this->authService->loginWithEmailOrPhone(
                identifier: $request->identifier,
                password: $request->password
            );

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Update last login timestamp
            $this->userService->updateLastLoginAt($user->id);

            return apiResponse([
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => new UserResource($user),
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ], trans('app.login_successfully'), 200);
        } catch (NotFoundException $e) {
            return apiResponse(null, $e->getMessage(), 400);
        } catch (Exception $e) {
            return apiResponse(null, $e->getMessage(), 500);
        }
    }

    /**
     * Handle user logout.
     */
    public function logout(): JsonResponse
    {
        try {
            $user = Auth::user();
            $user?->tokens()->delete(); // Revoke all tokens
            return apiResponse(null, trans('app.logout_successfully'), 200);
        } catch (Exception $e) {
            return apiResponse(null, 'Logout failed', 500);
        }
    }
}
