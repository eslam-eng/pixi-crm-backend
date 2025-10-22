<?php

namespace App\Http\Controllers\Api\Landlord\Auth;

use App\Enum\VerificationCodeType;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Landlord\SendVerificationCodeRequest;
use App\Services\Landlord\Actions\Auth\VerificationCodeService;
use App\Services\Landlord\UserService;

class SendVerificationCodeController extends Controller
{
    public function __construct(protected readonly VerificationCodeService $verificationService, protected readonly UserService $landlordUserService) {}

    public function __invoke(SendVerificationCodeRequest $sendVerificationCodeRequest)
    {
        $user = $this->landlordUserService->findByKey('email', $sendVerificationCodeRequest->email);

        if (! $user) {
            return ApiResponse::error(
                message: 'The provided email address is not registered in our system.',
                code: 404
            );
        }

        $code = $this->verificationService->sendVerificationCode(
            email: $sendVerificationCodeRequest->email,
            type: $sendVerificationCodeRequest->type,
            userName: $user->name
        );

        $success_message = $sendVerificationCodeRequest->type == VerificationCodeType::RESET_PASSWORD->value ? 'Reset code sent to your email, check your inbox' : 'Verification code sent to your email, check your inbox';

        return ApiResponse::success(data: ['code' => $code], message: $success_message);
    }
}
