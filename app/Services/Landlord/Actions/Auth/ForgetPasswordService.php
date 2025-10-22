<?php

namespace App\Services\Landlord\Actions\Auth;

use App\DTOs\Landlord\RestPasswordDTO;
use App\Enum\VerificationCodeType;
use App\Services\BaseService;
use App\Services\Landlord\UserService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ForgetPasswordService extends BaseService
{
    public function __construct(private readonly UserService $landlordUserService, private readonly VerificationCodeService $verificationCodeService) {}

    /**
     * @throws \Throwable
     */
    public function restPassword(RestPasswordDTO $dto)
    {
        return DB::connection('landlord')
            ->transaction(function () use ($dto) {
                $user = $this->landlordUserService->findByKey('email', $dto->email);
                // Verify the code
                $this->verificationCodeService->verifyCode(
                    email: $user->email,
                    type: VerificationCodeType::RESET_PASSWORD->value,
                    code: $dto->code
                );
                // Update password
                $user->password = Hash::make($dto->password);

                return $user->save();
            });

    }

    protected function getFilterClass(): ?string
    {
        return null;
    }

    protected function baseQuery(): Builder
    {
        return User::query();
    }
}
