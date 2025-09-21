<?php

namespace App\Notifications\Tenant;

use App\Models\Tenant\Task;
use App\Settings\TasksSettings;
use App\Traits\NotifyFcm;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskReminderNotification extends Notification
{
    use  NotifyFcm,Queueable;


    public $task;
    public $reminder;


    /**
     * Create a new notification instance.
     */
    public function __construct($task, $reminder)
    {
        $this->task = $task;
        $this->reminder = $reminder;
    }


  /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $settings = new TasksSettings();
        $channels = [];
        if ($settings->mail_notification) {
            $channels[] = 'mail';
        }
        if ($settings->system_notification) {
            $channels[] = 'database';
        }
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $subject = $this->reminder->time_unit === 'on_time' 
            ? "Task Due Now: {$this->task->title}"
            : "Task Reminder: {$this->task->title}";

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name}!")
            ->line($this->getReminderMessage())
            ->line("Task: {$this->task->title}")
            ->line("Description: {$this->task->description}")
            ->line("Due Date: {$this->task->due_date->format('M d, Y')} at {$this->task->due_time}")
            ->action('View Task', $this->getTaskUrl())
            ->line('Thank you for using our application!');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $isOnTime = $this->reminder->time_unit === 'on_time';
        $timeValue = $this->reminder->time_value;
        $timeUnit = $this->reminder->time_unit;

        // Store messages in all 4 languages for future use (similar to escalation)
        $messages = [
            'en' => $isOnTime
                ? 'Task (:title) is due now.'
                : 'Task (:title) is due in :value :unit.',
            'es' => $isOnTime
                ? 'La tarea (:title) vence ahora.'
                : 'La tarea (:title) vence en :value :unit.',
            'fr' => $isOnTime
                ? 'La tâche (:title) est due maintenant.'
                : 'La tâche (:title) est due dans :value :unit.',
            'ar' => $isOnTime
                ? 'المهمة (:title) مستحقة الآن.'
                : 'المهمة (:title) مستحقة خلال :value :unit.'
        ];

        // Replace placeholders in each language
        foreach ($messages as $lang => $message) {
            $replacements = [':title' => $this->task->title];
            if (!$isOnTime) {
                $replacements[':value'] = (string) $timeValue;
                $replacements[':unit'] = (string) $timeUnit;
            }
            $messages[$lang] = strtr($message, $replacements);
        }

        return [
            'task_title' => $this->task->title,
            'type' => 'task_reminder',
            'messages' => $messages, // Store all 4 languages like escalation
            'target_model' => Task::class,
            'target_id' => $this->task->id,
            'action_url' => '/tasks/' . $this->task->id,
        ];
    }


    /**
     * Get the reminder message based on reminder type.
     */
    private function getReminderMessage(): string
    {
        if ($this->reminder->time_unit === 'on_time') {
            return "Your task '{$this->task->title}' is due now!";
        }

        $timeValue = $this->reminder->time_value;
        $timeUnit = $this->reminder->time_unit;
        
        return "Your task '{$this->task->title}' is due in {$timeValue} {$timeUnit}.";
    }

    /**
     * Get the task URL for the action button.
     */
    private function getTaskUrl(): string
    {
        // This should be replaced with the actual task URL in your application
        //TODO: add here task link of FrontEnd Not Backend
        return url("/tasks/{$this->task->id}");
    }
}