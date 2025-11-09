<?php

namespace App\Notifications\Tenant;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


class CreateNewContactNotification extends Notification
{
    use Queueable;

    public $contact;

    /**
     * Create a new notification instance.
     */
    public function __construct($contact)
    {
        $this->contact = $contact;
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
            ->subject('Welcome to Our Application!')
            ->greeting('Hello!')
            ->line('A new contact has been created.')
            ->action('View Contact', url('/contacts/' . $this->contact->id))
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
            'message' => 'A new contact has been created: ' . $this->contact->name,
            'contact_id' => $this->contact->id,
            'contact_name' => $this->contact->name,
            'contact_email' => $this->contact->email,
            'action_url' => '/contacts/' . $this->contact->id,
            'type' => 'contact_created',
            'created_by' => auth()->id() ?? null, // If you have authentication
            'icon' => 'fas fa-user-plus', // For UI display
        ];
    }
}
