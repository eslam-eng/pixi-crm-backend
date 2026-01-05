<?php

namespace App\Mail\Central;

use App\Models\Central\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionActivated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Subscription $subscription,
        public ?string $actionUrl = null
    ) {
        $this->actionUrl = $actionUrl ?? url('/');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Subscription Activated',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $tenant = $this->subscription->tenant;
        $user = $tenant->owner;

        return new Content(
            view: 'emails.central.subscription_activated',
            with: [
                'name' => $user->first_name . ' ' . $user->last_name,
                'company_name' => $tenant->name,
                'plan_name' => $this->subscription->plan_name,
                'start_date' => $this->subscription->starts_at?->format('F j, Y'),
                'billing_cycle' => $this->subscription->billing_cycle?->getLabel(),
                'amount' => $this->subscription->amount . ' ' . $this->subscription->currency,
                'action_url' => $this->actionUrl,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
