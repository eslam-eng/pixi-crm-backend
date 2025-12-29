<?php

namespace App\Http\Controllers\Central\Api\Auth;

use App\DTO\Central\UserDTO;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\RegisterRequest;
use App\Services\Central\RegisterService;


class RegisterController extends Controller
{
    /**
     * @throws \Throwable
     */
    public function __invoke(RegisterRequest $request, RegisterService $registerService)
    {
        try {
            $userDTO = UserDTO::fromRequest($request);
            $userDTO->create_free_trial = $request->free_trial ?? false;
            $registerService->handle(registerDTO: $userDTO);
            return ApiResponse::success();
        } catch (\Exception $e) {
            return ApiResponse::error(message: 'there is an error please try again later or contact with support for fast response' . $e->getMessage());
        }
    }
}
