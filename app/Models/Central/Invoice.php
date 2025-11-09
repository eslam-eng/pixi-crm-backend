<?php

namespace App\Models\Central;

use App\Enums\Landlord\InvoiceStatusEnum;
use App\Enums\Landlord\PaymentMethodEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasUuids, Filterable, HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'invoice_number',
        'tenant_id',
        'subscription_id',
        'subtotal',
        'tax_amount',
        'discount_percentage',
        'total',
        'currency',
        'status',
        'due_date',
        'paid_at',
        'metadata',
        'payment_method',
        'payment_reference',
        'billing_address',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'total' => 'decimal:2',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
        'billing_address' => 'array',
        'metadata' => 'array',
        'status' => InvoiceStatusEnum::class,
        'payment_method' => PaymentMethodEnum::class,
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = static::generateInvoiceNumber();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    public function items(): HasMany|Invoice
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isPaid(): bool
    {
        return $this->status == InvoiceStatusEnum::PAID->value;
    }

    public function isPending(): bool
    {
        return $this->status == InvoiceStatusEnum::PENDING->value;
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && ! $this->isPaid();
    }

    public function discountCodeUsage(): HasMany|Invoice
    {
        return $this->hasMany(DiscountCodeUsage::class);
    }

    public function markAsPaid(?string $paymentMethod = null, ?string $paymentReference = null): void
    {
        $this->update([
            'status' => InvoiceStatusEnum::PAID->value,
            'paid_at' => now(),
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update([
            'status' => InvoiceStatusEnum::FAILED->value,
        ]);
    }

    public function getFormattedTotal(): string
    {
        return number_format($this->total, 2) . ' ' . $this->currency;
    }

    public static function generateInvoiceNumber(): string
    {
        $prefix = config('invoices.prefix', 'INV');
        $year = date('Y');
        $month = date('m');

        // Get the max sequence for the current month
        $lastSequence = static::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->selectRaw('MAX(CAST(RIGHT(invoice_number, 4) AS UNSIGNED)) as max_seq')
            ->value('max_seq');

        $sequence = $lastSequence ? $lastSequence + 1 : 1;

        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $sequence);
    }
}
