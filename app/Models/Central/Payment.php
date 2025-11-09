<?php

namespace App\Models\Central;

use App\Enums\Landlord\PaymentStatusEnum;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use Filterable, HasFactory;

    protected $fillable = [
        'payment_id',
        'invoice_id',
        'user_id',
        'tenant_id',
        'amount',
        'currency',
        'status',
        'gateway',
        'gateway_transaction_id',
        'gateway_response',
        'processed_at',
        'failure_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'processed_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->payment_id)) {
                $payment->payment_id = 'pay_' . uniqid();
            }
        });
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isCompleted(): bool
    {
        return $this->status == PaymentStatusEnum::COMPLETED->value;
    }

    public function isFailed(): bool
    {
        return $this->status == PaymentStatusEnum::FAILED->value;
    }

    public function markAsCompleted(?string $transactionId = null, ?array $gatewayResponse = null): void
    {
        $this->update([
            'status' => PaymentStatusEnum::COMPLETED->value,
            'processed_at' => now(),
            'gateway_transaction_id' => $transactionId,
            'gateway_response' => $gatewayResponse,
        ]);

        // Mark invoice as paid
        $this->invoice->markAsPaid($this->gateway, $this->gateway_transaction_id);
    }

    public function markAsFailed(?string $reason = null, ?array $gatewayResponse = null): void
    {
        $this->update([
            'status' => PaymentStatusEnum::FAILED->value,
            'processed_at' => now(),
            'failure_reason' => $reason,
            'gateway_response' => $gatewayResponse,
        ]);

        // Mark invoice as failed
        $this->invoice->update(['status' => PaymentStatusEnum::FAILED->value]);
    }
}
