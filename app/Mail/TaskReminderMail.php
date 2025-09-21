<?php

namespace App\Mail;

use App\Models\Tenant\Task;
use App\Models\Tenant\Reminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Task $task,
        public Reminder $reminder,
        public string $userName
    ) {
        // Set queue settings
        $this->onQueue('emails');
        $this->delay(now()->addSeconds(5)); // Optional delay
    }

    public function envelope(): Envelope
    {
        $subject = $this->reminder->time_unit === 'on_time' 
            ? "Task Due Now: {$this->task->title}"
            : "Task Reminder: {$this->task->title}";

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.task-reminder',
            text: 'emails.task-reminder-text',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}