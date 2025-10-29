<?php

namespace App\Listeners\UserRegistered;

use App\Events\UserRegistered;
use App\Services\Landlord\Actions\Auth\VerificationCodeService;

class SendVerificationCodeListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected VerificationCodeService $verificationCodeService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        $user = $event->user;
        $this->verificationCodeService->sendVerificationCode(
            email: $user->email,
            type: 'email_verification',
            userName: $user->name
        );

    }
}
