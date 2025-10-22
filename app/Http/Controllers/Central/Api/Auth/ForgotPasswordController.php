<?php

namespace App\Http\Controllers\Api\Landlord\Auth;

use App\DTOs\Landlord\RestPasswordDTO;
use App\Exceptions\VerificationCode\ActivationCodeException;
use App\Exceptions\VerificationCode\CodeNotFoundException;
use App\Exceptions\VerificationCode\MaxAttemptsExceededException;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Landlord\RestPasswordRequest;
use App\Services\Landlord\Actions\Auth\ForgetPasswordService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ForgotPasswordController extends Controller
{
    public function __construct(protected readonly ForgetPasswordService $forgetPasswordService) {}

    /**
     * @throws \Throwable
     */
    public function resetPassword(RestPasswordRequest $request)
    {
        $restPasswordDTO = RestPasswordDTO::fromRequest($request);
        try {
            $this->forgetPasswordService->restPassword(dto: $restPasswordDTO);

            return ApiResponse::success(message: 'Password reset successful');
        } catch (NotFoundHttpException $exception) {
            return ApiResponse::notFound(message: $exception->getMessage());
        } catch (CodeNotFoundException|ActivationCodeException|MaxAttemptsExceededException $exception) {
            return ApiResponse::error(message: $exception->getMessage());
        }
    }
}
