<?php

namespace App\Mail;

use App\Models\Tenant\FormSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FormSubmissionMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public FormSubmission $submission,
        public array $emailSettings
    ) {
        // Set queue settings
        $this->onQueue('emails');
        $this->delay(now()->addSeconds(5)); // Optional delay
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->emailSettings['to'],
            subject: $this->emailSettings['subject'] ?? 'New Form Submission',
            replyTo: array_filter([$this->getReplyToEmail()]),
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.form-submission',
            text: 'emails.form-submission-text', // Optional text version
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        // Handle file uploads if any
        foreach ($this->submission->data as $key => $value) {
            if (is_string($value) && str_starts_with($value, 'storage/')) {
                $attachments[] = storage_path('app/public/' . str_replace('storage/', '', $value));
            }
        }

        return $attachments;
    }

    /**
     * Get reply-to email from form data
     */
    private function getReplyToEmail(): ?string
    {
        $data = $this->submission->data;

        // Look for common email field names
        $emailFields = ['email', 'email_address', 'user_email', 'contact_email'];

        foreach ($emailFields as $field) {
            if (!empty($data[$field]) && filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                return $data[$field];
            }
        }

        return null;
    }

    /**
     * Handle failed job
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Form submission email failed', [
            'form_id' => $this->submission->form_id,
            'submission_id' => $this->submission->id,
            'error' => $exception->getMessage(),
            'to' => $this->emailSettings['to'],
        ]);
    }
}
