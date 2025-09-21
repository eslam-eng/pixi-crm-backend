<?php

namespace App\Http\Controllers\Central\Api;

use App\Services\Central\AuthService;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Auth\LoginRequest;
use App\Http\Requests\Central\Auth\SignupRequest;
use App\Http\Resources\LandlordUser\LandlordUserResource;
use DB;
use Exception;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function signup(SignupRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();
            $user = $this->authService->signup(
                name: $request->name,
                tenant: $request->tenant,
                email: $request->email,
                password: $request->password,
                activationCode: $request->activation_code,
                image: $request->file('image')
            );
            DB::commit();
            return apiResponse(new LandlordUserResource($user), trans('app.signup_successfully'), 201);
        } catch (Exception $e) {
            DB::rollBack();
            return apiResponse(null, $e->getMessage(), 500);
        }
    }

    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = $this->authService->loginWithEmailOrPhone(identifier: $request->identifier, password: $request->password);
            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            return apiResponse([
                'token' => $token,
                'user' => new LandlordUserResource($user)
            ], trans('app.login_successfully'), 200);
        } catch (NotFoundException $e) {
            return apiResponse(null, $e->getMessage(), 401);
        } catch (Exception $e) {
            return apiResponse(null, $e->getMessage(), 500);
        }
    }

    public function logout()
    {
        try {
            $user = Auth::guard('sanctum')->user();
            $user?->tokens()->delete(); // Revoke all tokens
            return apiResponse(null, trans('app.logout_successfully'), 200);
        } catch (Exception $e) {
            return apiResponse(null, 'Logout failed', 500);
        }
    }
}
