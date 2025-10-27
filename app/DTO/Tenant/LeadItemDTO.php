<?php

namespace App\DTO\Tenant;

use Illuminate\Support\Arr;

class LeadItemDTO
{
    public function __construct(
        public ?int $item_id,
        public ?int $variant_id,
        public ?int $quantity,
        public ?float $price,
    ) {}

    public function toArray(): array
    {
        return [
            'item_id' => $this->item_id,
            'variant_id' => $this->variant_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            item_id: Arr::get($data, 'item_id'),
            variant_id: Arr::get($data, 'variant_id'),
            quantity: Arr::get($data, 'quantity'),
            price: Arr::get($data, 'price'),
        );
    }
}
