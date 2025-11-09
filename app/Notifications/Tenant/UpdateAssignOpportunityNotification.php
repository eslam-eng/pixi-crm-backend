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
        return ['mail', 'database'];
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

    /**
     * Get the array representation for database storage.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => 'The opportunity has been assigned to'. $this->opportunity->user->name,
            'opportunity_id' => $this->opportunity->id,
            'opportunity_name' => $this->opportunity->name,
            'opportunity_user_id' => $this->opportunity->user->id,
            'opportunity_user_name' => $this->opportunity->user->name,
            'action_url' => '/opportunities/' . $this->opportunity->id,
            'type' => 'opportunity_assigned',
            'created_by' => auth()->id() ?? null, // If you have authentication
            'icon' => 'fas fa-user-plus', // For UI display
        ];
    }
}
