<?php

namespace App\Services\Central;

use App\Exceptions\VerificationCode\ActivationCodeException;
use App\Exceptions\VerificationCode\CodeNotFoundException;
use App\Exceptions\VerificationCode\MaxAttemptsExceededException;
use App\Models\Central\VerificationCode;
use App\Services\Central\BaseService;

class VerificationCodeService extends BaseService
{
    protected function getFilterClass(): ?string
    {
        return null;
    }

    protected function baseQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return VerificationCode::query();
    }

    /**
     * Generate and send verification code
     */
    public function sendVerificationCode(
        string $email,
        string $type,
        ?string $userName = null,
    ): string {
        // Invalidate any existing active codes
        $this->getQuery()->where('email', $email)
            ->where('type', $type)->delete();

        $code = $this->generateCode(6);
        $this->getQuery()->create([
            'email' => $email,
            'code' => $code,
            'type' => $type,
            'expires_at' => now()->addMinutes(15),

        ]);

        // Send email
        //        Mail::to($email)->queue(new VerificationCodeMail(
        //            code: $code,
        //            type: $type,
        //            userName: $userName
        //        ));

        return $code;
    }

    /**
     * Verify the code
     *
     * @throws CodeNotFoundException
     * @throws MaxAttemptsExceededException
     * @throws ActivationCodeException
     */
    public function verifyCode(string $email, string $type, string $code): bool
    {
        $verificationCode = VerificationCode::where('email', $email)
            ->where('type', $type)
            ->where('code', $code)
            ->latest()
            ->first();

        if (! $verificationCode) {
            throw new CodeNotFoundException;
        }
        if ($verificationCode->isExpired()) {
            $verificationCode->delete();
            throw new ActivationCodeException;
        }
        if ($verificationCode->hasExceededMaxAttempts()) {
            $verificationCode->delete();
            throw new MaxAttemptsExceededException;
        }
        // Increment attempts before checking code
        $verificationCode->increment('attempts');

        if ($verificationCode->code === $code) {
            $verificationCode->delete();

            return true;
        }

        // If we reach here, the code didn't match
        throw new CodeNotFoundException;
    }

    /**
     * Generate verification code
     */
    private function generateCode(int $length): string
    {
        return str_pad((string) random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    }
}
