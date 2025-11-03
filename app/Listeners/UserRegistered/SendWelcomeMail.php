<?php

namespace App\Listeners\UserRegistered;

use App\Events\UserRegistered;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;


class SendWelcomeMail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        Log::info('🎯 [Listener] SendWelcomeMail triggered', [
            'user_id' => $event->user->id,
            'user_name' => $event->user->first_name . ' ' . $event->user->last_name,
            'email' => $event->user->email,
        ]);
        // set config mail for landlord
        $user = $event->user;
        Mail::to($user->email)->queue(new WelcomeEmail(user: $user));

    }
}
