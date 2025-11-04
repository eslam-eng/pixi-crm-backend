<?php

namespace App\Mail;

use App\Models\Admin;
use App\Models\Tenant\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param  mixed  $user
     * @return void
     */
    public function __construct(
        public User|Admin $user,
        public string $password,
        public ?string $loginUrl = null
    ) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Account Credentials - ' . config('app.name'))
            ->view('emails.user-credentials');
    }
}
