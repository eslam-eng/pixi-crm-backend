<?php

namespace App\DTO\Central;

use App\DTO\BaseDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SourcePayoutBatchDTO extends BaseDTO
{
    public function __construct(
        public int $plan_id,   // Package ID
        public int $source_id,
        public string $period_start,
        public string $period_end,
        public int $total_amount = 0,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            plan_id: Arr::get($data, 'plan_id'),
            source_id: Arr::get($data, 'source_id'),
            period_start: Arr::get($data, 'period_start'),
            period_end: Arr::get($data, 'period_end'),
            total_amount: Arr::get($data, 'total_amount', 0),
        );
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            plan_id: $request->plan_id,
            source_id: $request->source_id,
            period_start: $request->period_start,
            period_end: $request->period_end,
            total_amount: $request->total_amount ?? 0,
        );
    }

    public function toArray(): array
    {
        return [
            'plan_id' => $this->plan_id,
            'source_id' => $this->source_id,
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
            'total_amount' => $this->total_amount,
        ];
    }
}
