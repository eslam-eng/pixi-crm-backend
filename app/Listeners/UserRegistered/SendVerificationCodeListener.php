<?php

namespace App\Listeners\UserRegistered;

use App\Events\UserRegistered;
use App\Services\Central\VerificationCodeService;
use Illuminate\Support\Facades\Log;


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
        Log::info('🎯 [Listener] SendVerificationCodeListener triggered', [
            'user_id' => $event->user->id,
            'user_name' => $event->user->first_name . ' ' . $event->user->last_name,
            'email' => $event->user->email,
        ]);
        $user = $event->user;
        $this->verificationCodeService->sendVerificationCode(
            email: $user->email,
            type: 'email_verification',
            userName: $user->name
        );
    }
}
