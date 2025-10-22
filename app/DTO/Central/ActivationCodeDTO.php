<?php

namespace App\DTO\Central;

use App\DTO\BaseDTO;
use App\Enums\Central\ActivationCodeStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ActivationCodeDTO extends BaseDTO
{
    public function __construct(
        public int $planId,   // Package ID
        public int $source_id,
        public int $validityDays, // Validity in days
        public int $count = 1,     // Number of codes to generate
        public int $parts = 2,        // Number of parts per code
        public int $partLength = 3,   // Length of each part
        public string $status = ActivationCodeStatusEnum::AVAILABLE->value,
        public ?string $expire_at = null,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            planId: Arr::get($data, 'plan_id'),
            source_id: Arr::get($data, 'source_id'),
            validityDays: Arr::get($data, 'validity_days'),
            count: Arr::get($data, 'count', 1),
            parts: Arr::get($data, 'parts', 2),
            partLength: Arr::get($data, 'part_length', 3),
            status: Arr::get($data, 'status', true),
            expire_at: Arr::get($data, 'expire_at'),
        );
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            planId: $request->plan_id,
            source_id: $request->source_id,
            validityDays: $request->validity_days,
            count: $request->count ?? 1,
            parts: $request->parts ?? 2,
            partLength: $request->part_length ?? 3,
            status: $request->status,
            expire_at: $request->expire_at,
        );
    }

    public function toArray(): array
    {
        return [
            'plan_id' => $this->planId,
            'source_id' => $this->source_id,
            'validity_days' => $this->validityDays,
            'status' => $this->status ?? ActivationCodeStatusEnum::AVAILABLE->value,
            'expire_at' => $this->expire_at,
        ];
    }
}
