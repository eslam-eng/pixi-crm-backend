<?php

namespace App\Notifications\Tenant;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


class UpdateAssignOpportunityNotification extends Notification
{
    use Queueable;

    public $opportunity;

    /**
     * Create a new notification instance.
     */
    public function __construct($opportunity)
    {
        $this->opportunity = $opportunity;
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
        return (new MailMessage)
            ->subject('Opportunity Assigned to '. $this->opportunity->user->name)
            ->greeting('Hello!')
            ->line('The opportunity has been assigned to'. $this->opportunity->user->name)
            ->action('View Opportunity', url('/opportunities/' . $this->opportunity->id))
            ->line('Thank you for using our application!');
    }
}
