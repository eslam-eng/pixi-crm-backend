<?php

namespace App\DTO\Central;

use App\DTO\BaseDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SourceDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public float $payout_percentage = 0,
        public bool $is_active = true,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            name: Arr::get($data, 'name'),
            payout_percentage: Arr::get($data, 'payout_percentage', 0),
            is_active: Arr::get($data, 'is_active', true),
        );
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            name: $request->name,
            payout_percentage: $request->payout_percentage ?? 0,
            is_active: $request->boolean('is_active', true),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'payout_percentage' => $this->payout_percentage,
            'is_active' => $this->is_active,
        ];
    }
}
