<?php

namespace App\Mail;

use App\Models\Tenant\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class OpportunityItemsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Lead $lead,
        public Collection $items,
        public array $selectedColumns = [],
        public ?string $subjectLine = null
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine ?? 'Opportunity Items Details',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.opportunity_items',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
