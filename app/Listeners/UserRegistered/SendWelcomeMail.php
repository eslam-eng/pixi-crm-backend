<?php

namespace App\Listeners\UserRegistered;

use App\Events\UserRegistered;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;

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
        // set config mail for landlord
        $user = $event->user;
        Mail::to($user->email)->queue(new WelcomeEmail(user: $user));

    }
}
