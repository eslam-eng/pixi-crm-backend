<?php

namespace App\DTO\Tenant;

class DealPaymentDTO
{
    public function __construct(
        public readonly float $amount,
        public readonly string $pay_date,
        public readonly int $payment_method_id
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            amount: (float) $data['amount'],
            pay_date: $data['pay_date'],
            payment_method_id: (int) $data['payment_method_id']
        );
    }

    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'pay_date' => $this->pay_date,
            'payment_method_id' => $this->payment_method_id,
        ];
    }
}
