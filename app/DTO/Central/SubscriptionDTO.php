<?php

namespace App\DTO\Central;

use App\DTO\BaseDTO;
use App\Enum\ActivationStatusEnum;
use App\Enum\SubscriptionStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SubscriptionDTO extends BaseDTO
{
    public function __construct(
        public int $plan_id,
        public string $tenant_id,
        public string $starts_at,
        public int $amount,
        public ?string $billing_cycle = null,
        public ?string $ends_at = null,
        public ?string $trial_ends_at = null,
        public bool $auto_renew = ActivationStatusEnum::INACTIVE->value,
        public ?string $status = SubscriptionStatusEnum::ACTIVE->value,
        public ?array $plan_snapshot = null,
        public ?string $activation_code_id = null,
        public ?InvoiceDTO $invoiceDTO = null,
        public bool $shouldCreateInvoice = true,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            plan_id: Arr::get($data, 'plan_id'),
            tenant_id: Arr::get($data, 'tenant_id'),
            starts_at: Arr::get($data, 'starts_at'),
            amount: Arr::get($data, 'amount'),
            billing_cycle: Arr::get($data, 'billing_cycle'),
            ends_at: Arr::get($data, 'ends_at'),
            trial_ends_at: Arr::get($data, 'trial_ends_at'),
            auto_renew: Arr::get($data, 'auto_renew'),
            status: Arr::get($data, 'status'),
            plan_snapshot: Arr::get($data, 'plan_snapshot'),
            activation_code_id: Arr::get($data, 'activation_code_id'),

        );
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            plan_id: $request->plan_id,
            tenant_id: $request->tenant_id,
            starts_at: $request->starts_at,
            amount: $request->amount,
            billing_cycle: $request->billing_cycle,
            ends_at: $request->ends_at,
            trial_ends_at: $request->trial_ends_at,
            auto_renew: $request->auto_renew,
            status: $request->status,
            plan_snapshot: $request->plan_snapshot,
            activation_code_id: $request->activation_code_id,
        );
    }

    public function toArray(): array
    {
        return [
            'plan_id' => $this->plan_id,
            'tenant_id' => $this->tenant_id,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'trial_ends_at' => $this->trial_ends_at,
            'auto_renew' => $this->auto_renew,
            'status' => $this->status,
            'plan_snapshot' => $this->plan_snapshot,
            'billing_cycle' => $this->billing_cycle,
            'activation_code_id' => $this->activation_code_id,
            'amount' => $this->amount,
        ];
    }
}
