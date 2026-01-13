<?php

namespace App\Notifications\Central;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SetupPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $token;
    public $email;

    /**
     * Create a new notification instance.
     */
    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Adjust the URL to your frontend's actual route
        // Assuming env variable FRONTEND_URL or similar, effectively:
        $url = config('app.frontend_url') . '/auth/setup-password?token=' . $this->token . '&email=' . urlencode($this->email);

        return (new MailMessage)
            ->subject('Setup Your Password')
            ->greeting('Hello!')
            ->line('You have been registered. Please click the button below to set up your password.')
            ->action('Setup Password', $url)
            ->line('If you did not create an account, no further action is required.')
            ->line('Thank you!');
    }
}
