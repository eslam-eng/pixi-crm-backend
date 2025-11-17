<?php

namespace App\Services\Central\Stripe;

use App\Models\Central\Invoice;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class StripeService
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret_key'));
    }

    public function pay(Invoice $invoice)
    {
        return $this->stripe->paymentIntents->create(params: [
            'amount' => (int) round($invoice->total * 100), // cents
            'currency' => $invoice->currency,
            'capture_method' => 'automatic', // or 'manual' if you want delayed capture
            'metadata' => [
                'invoice_id' => (string) $invoice->id,
                'user_id' => (string) $invoice->tenant->owner_id,
            ],
            'receipt_email' => (string) $invoice->tenant->owner->email,
        ], opts: [
            // optional: idempotency key to avoid duplicates if client resubmits
            'idempotency_key' => 'create-sub-' . $invoice->id,
        ]);
    }

    protected function formatError(ApiErrorException $e, string $type): array
    {
        $error = $e->getError();

        return [
            'success' => false,
            'type' => $type,
            'message' => $this->userFriendlyMessage($error?->code, $error?->decline_code),
            'stripe_message' => $error?->message, // useful for logs
            'code' => $error?->code,
            'decline_code' => $error?->decline_code,
            'param' => $error?->param,
            'doc_url' => $error?->doc_url,
            'http_status' => $e->getHttpStatus(),
        ];
    }

    protected function userFriendlyMessage(?string $code, ?string $declineCode): string
    {
        if ($declineCode && trans()->has("stripe.$declineCode")) {
            return __("stripe.$declineCode");
        }

        if ($code && trans()->has("stripe.$code")) {
            return __("stripe.$code");
        }

        return __('stripe.default');
    }
}
