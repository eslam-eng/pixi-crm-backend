<?php

namespace App\DTO\Central;

use App\DTO\BaseDTO;
use App\Enum\InvoiceStatusEnum;
use App\Models\Landlord\DiscountCode;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class InvoiceDTO extends BaseDTO
{
    public function __construct(
        public string $tenant_id,
        public ?int $user_id = null,
        public ?string $subscription_id = null,
        public ?float $subtotal = 0,
        public ?float $tax_amount = 0,
        public ?float $discount_percentage = 0,
        public ?float $total = 0,
        public ?string $currency = 'USD',
        public ?string $status = InvoiceStatusEnum::PENDING->value,
        public ?string $due_date = null,
        public ?string $notes = null,
        public ?string $payment_method = null,
        public ?string $paid_at = null,
        public DiscountCode|string|null $discountCode = null,
        public array $invoiceItems = [],
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            tenant_id: Arr::get($data, 'tenant_id'),
            user_id: Arr::get($data, 'user_id'),
            subscription_id: Arr::get($data, 'subscription_id'),
            subtotal: Arr::get($data, 'subtotal', 0),
            tax_amount: Arr::get($data, 'tax_amount', 0),
            total: Arr::get($data, 'total', 0),
            currency: Arr::get($data, 'currency', 'USD'),
            status: Arr::get($data, 'status', 'pending'),
            due_date: Arr::get($data, 'due_date'),
            notes: Arr::get($data, 'notes'),
            discountCode: Arr::get($data, 'discount_code'),
            invoiceItems: Arr::get($data, 'invoiceItems'),
            payment_method: Arr::get($data, 'payment_method'),
            paid_at: Arr::get($data, 'paid_at'), );
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            tenant_id: $request->tenant_id,
            user_id: $request->user_id,
            subscription_id: $request->subscription_id,
            subtotal: $request->subtotal ?? 0,
            tax_amount: $request->tax_amount ?? 0,
            discount_percentage: $request->discount_percentage ?? 0,
            total: $request->total ?? 0,
            currency: $request->currency ?? 'USD',
            status: $request->status ?? 'pending',
            due_date: $request->due_date,
            notes: $request->notes,
            discountCode: $request->discount_code,
            invoiceItems: $request->invoiceItems,
            payment_method: $request->payment_method,
            paid_at: $request->paid_at,
        );
    }

    public function toArray(): array
    {
        return [
            'tenant_id' => $this->tenant_id,
            'user_id' => $this->user_id,
            'subscription_id' => $this->subscription_id,
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->tax_amount,
            'discount_percentage' => $this->discount_percentage,
            'total' => $this->total,
            'currency' => $this->currency,
            'status' => $this->status,
            'due_date' => $this->due_date,
            'payment_method' => $this->payment_method,
            'paid_at' => $this->paid_at,
            'notes' => $this->notes,
        ];
    }
}
