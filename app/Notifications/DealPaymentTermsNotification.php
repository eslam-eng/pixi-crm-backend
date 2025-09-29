<?php

namespace App\Notifications;

use App\Models\Tenant\Deal;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DealPaymentTermsNotification extends Notification
{

    public function __construct(
        public Deal $deal,
        public string $paymentTerms
    ) {
        // Notification sent immediately without queuing
    }

    /**
     * Get the notification's delivery channels.
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
        $contact = $this->deal->lead->contact ?? $notifiable;
        
        return (new MailMessage)
            ->subject("Payment Terms for Deal: {$this->deal->deal_name}")
            ->greeting("Hello {$contact->first_name} {$contact->last_name}!")
            ->line("We're excited to work with you on this deal. Please review the payment terms below.")
            ->line("**Deal:** {$this->deal->deal_name}")
            ->line("**Type:** " . ucfirst(str_replace('_', ' ', $this->deal->deal_type)))
            ->line("**Total Amount:** $" . number_format($this->deal->total_amount, 2))
            ->line("**Sale Date:** " . \Carbon\Carbon::parse($this->deal->sale_date)->format('M d, Y'))
            ->line("**Payment Status:** " . ucfirst($this->deal->payment_status))
            ->when($this->deal->payment_status === 'partial', function ($mail) {
                return $mail
                    ->line("**Partial Amount Paid:** $" . number_format($this->deal->partial_amount_paid, 2))
                    ->line("**Remaining Amount Due:** $" . number_format($this->deal->amount_due, 2));
            })
            ->when($this->deal->payment_status === 'unpaid', function ($mail) {
                return $mail->line("**Amount Due:** $" . number_format($this->deal->amount_due, 2));
            })
            ->when($this->deal->notes, function ($mail) {
                return $mail
                    ->line("**Deal Notes:**")
                    ->line($this->deal->notes);
            })
            ->line("**Payment Terms:**")
            ->line($this->paymentTerms)
            ->line("This is an automated notification regarding your deal.")
            ->line("If you have any questions about payment terms or this deal, please contact us immediately.")
            ->salutation('Thank you for your business!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'deal_id' => $this->deal->id,
            'deal_name' => $this->deal->deal_name,
            'payment_status' => $this->deal->payment_status,
            'total_amount' => $this->deal->total_amount,
            'amount_due' => $this->deal->amount_due,
        ];
    }

    /**
     * Handle failed job
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Deal payment terms notification failed', [
            'deal_id' => $this->deal->id,
            'contact_email' => $this->deal->lead->contact->email ?? 'unknown',
            'error' => $exception->getMessage(),
        ]);
    }
}
