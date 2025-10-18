<?php

namespace App\Mail;

use App\Models\Tenant\Report;
use App\Models\Tenant\ReportExecution;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class ReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Report $report,
        public ReportExecution $execution,
        public string $fileName,
        public string $fileContent,
        public string $mimeType
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Scheduled Report: {$this->report->name}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.report',
            with: [
                'report' => $this->report,
                'execution' => $this->execution,
                'fileName' => $this->fileName,
                'generatedAt' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn() => $this->fileContent,
                $this->fileName
            )->withMime($this->mimeType),
        ];
    }
}
