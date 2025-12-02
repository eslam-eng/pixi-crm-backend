<?php

namespace App\Notifications\Tenant;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


class AutomationManagerNotification extends Notification
{
    use Queueable;

    public $user;
    public $message;
    public $triggerable;

    /**
     * Create a new notification instance.
     */
    public function __construct($user, $message, $triggerable)
    {
        $this->user = $user;
        $this->message = $message;
        $this->triggerable = $triggerable;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [ 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Automation Triggered')
            ->greeting('Hello!')
            ->line($this->message)
            ->action('View Contact', url('/users/' . $this->user->id))
            ->line('Thank you for using our application!');
    }

        /**
     * Get the array representation for database storage.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
            'action_url' => '/users/' . $this->user->id,
            'type' => 'user_created',
            'created_by' => auth()->id() ?? null, // If you have authentication
            'icon' => 'fas fa-user-plus', // For UI display
        ];
    }
}
