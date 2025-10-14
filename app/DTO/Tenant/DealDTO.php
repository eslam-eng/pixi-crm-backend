<?php

namespace App\DTO\Tenant;

use App\DTO\BaseDTO;
use Illuminate\Support\Arr;

class DealDTO extends BaseDTO
{
    public function __construct(
        public string $deal_name,
        public ?int $lead_id = null,
        public ?int $chair_id = null,
        public ?string $sale_date = null,
        public ?string $discount_type = null,
        public ?float $discount_value = null,
        public ?float $tax_rate = null,
        public ?string $payment_status = null,
        public ?int $payment_method_id = null,
        public ?string $notes = null,
        public ?int $assigned_to_id = null,
        public ?float $total_amount = null,
        public ?float $partial_amount_paid = null,
        public ?float $amount_due = null,
        public ?string $approval_status = null,
        public ?array $items = null,
        public ?array $attachments = null,
    ) {}

    public static function fromRequest($request): BaseDTO
    {
        return new self(
            deal_name: $request->input('deal_name'),
            lead_id: $request->input('lead_id'),
            chair_id: $request->input('chair_id'),
            sale_date: $request->input('sale_date'),
            discount_type: $request->input('discount_type'),
            discount_value: $request->input('discount_value'),
            tax_rate: $request->input('tax_rate'),
            payment_status: $request->input('payment_status'),
            payment_method_id: $request->input('payment_method_id'),
            notes: $request->input('notes'),
            assigned_to_id: $request->input('assigned_to_id'),
            total_amount: $request->input('total_amount'),
            partial_amount_paid: $request->input('partial_amount_paid'),
            amount_due: $request->input('amount_due'),
            approval_status: $request->input('approval_status'),
            items: $request->input('items'),
            attachments: $request->file('attachments'),
        );
    }

    public function toArray(): array
    {
        return [
            'deal_name' => $this->deal_name,
            'lead_id' => $this->lead_id,
            'chair_id' => $this->chair_id,
            'sale_date' => $this->sale_date,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'tax_rate' => $this->tax_rate,
            'payment_status' => $this->payment_status,
            'payment_method_id' => $this->payment_method_id,
            'notes' => $this->notes,
            'assigned_to_id' => $this->assigned_to_id,
            'total_amount' => $this->total_amount,
            'partial_amount_paid' => $this->partial_amount_paid,
            'amount_due' => $this->amount_due,
            'approval_status' => $this->approval_status,
            'items' => $this->items,
            'attachments' => $this->attachments,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            deal_name: Arr::get($data, 'deal_name'),
            lead_id: Arr::get($data, 'lead_id'),
            chair_id: Arr::get($data, 'chair_id'),
            sale_date: Arr::get($data, 'sale_date'),
            discount_type: Arr::get($data, 'discount_type'),
            discount_value: Arr::get($data, 'discount_value'),
            tax_rate: Arr::get($data, 'tax_rate'),
            payment_status: Arr::get($data, 'payment_status'),
            payment_method_id: Arr::get($data, 'payment_method_id'),
            notes: Arr::get($data, 'notes'),
            assigned_to_id: Arr::get($data, 'assigned_to_id'),
            total_amount: Arr::get($data, 'total_amount'),
            partial_amount_paid: Arr::get($data, 'partial_amount_paid'),
            amount_due: Arr::get($data, 'amount_due'),
            approval_status: Arr::get($data, 'approval_status'),
            items: Arr::get($data, 'items'),
            attachments: Arr::get($data, 'attachments'),
        );
    }
}
