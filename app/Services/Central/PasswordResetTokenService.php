<?php

namespace App\Services\Central;

use App\Models\Central\PasswordResetToken;
use Illuminate\Support\Str;

class PasswordResetTokenService
{
    /**
     * Create or update a password reset token for the given email.
     *
     * @param string $email
     * @return string
     */
    public function createToken(string $email): string
    {
        $token = Str::random(60);

        PasswordResetToken::updateOrInsert(
            ['email' => $email],
            [
                'token' => $token,
                'created_at' => now(),
            ]
        );

        return $token;
    }

    /**
     * Delete a token for the given email.
     *
     * @param string $email
     * @return void
     */
    public function deleteToken(string $email): void
    {
        PasswordResetToken::where('email', $email)->delete();
    }
}
