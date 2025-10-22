<?php

namespace App\Http\Controllers\Api\Landlord\Auth;

use App\DTOs\UserDTO;
use App\Exceptions\VerificationCode\ActivationCodeException;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Landlord\RegisterWithActivationCodeRequest;
use App\Http\Resources\Tenant\AuthUserResource;
use App\Services\Landlord\Actions\Auth\RegisterService;
use Illuminate\Support\Facades\DB;
use PHPUnit\Event\InvalidArgumentException;

class RegisterWithActivationCodeController extends Controller
{
    /**
     * @throws \Throwable
     */
    public function __invoke(
        RegisterWithActivationCodeRequest $request,
        RegisterService $registerService)
    {
        try {
            $userDTO = UserDTO::fromRequest($request);

            $user = DB::connection('landlord')->transaction(function () use ($userDTO, $registerService) {
                return $registerService->handle(registerDTO: $userDTO);
            });

            $data = [
                'token' => $user->generateToken(),
                'user' => AuthUserResource::make($user),
            ];

            return ApiResponse::success(data: $data);
        } catch (ActivationCodeException|InvalidArgumentException $exception) {
            DB::connection('landlord')->rollBack();

            return ApiResponse::error(message: $exception->getMessage());
        } catch (\Exception $e) {
            logger($e);
            DB::connection('landlord')->rollBack();

            return ApiResponse::error(message: 'there is an error please try again later or contact with support for fast response');
        }
    }
}
