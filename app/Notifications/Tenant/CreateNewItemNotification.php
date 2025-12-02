<?php

namespace App\Notifications\Tenant;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


class CreateNewItemNotification extends Notification
{
    use Queueable;

    public $item;

    /**
     * Create a new notification instance.
     */
    public function __construct($item)
    {
        $this->item = $item;
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
            ->subject('New Item Created')
            ->greeting('Hello!')
            ->line('A new ' . $this->item->type?->value . ' has been created: '  . $this->item->name)
            ->action('View Item', url('/items/' . $this->item->id))
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
            'message' => 'A new ' . $this->item->type?->value . ' has been created: '  . $this->item->name,
            'item_id' => $this->item->id,
            'item_name' => $this->item->name,
            'item_type' => $this->item->type?->value,
            'action_url' => '/items/' . $this->item->id,
            'type' => 'item_created',
            'created_by' => user_id() ?? null, // If you have authentication
            'icon' => 'fas fa-plus', // For UI display
        ];
    }
}
