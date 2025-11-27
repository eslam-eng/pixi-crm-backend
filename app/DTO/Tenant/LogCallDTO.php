<?php

namespace App\DTO\Tenant;

use Illuminate\Support\Arr;

class LogCallDTO
{
    public function __construct(
        public string $call_notes,
        public string $call_direction,
    ) {}

    public function toArray(): array
    {
        return [
            'call_notes' => $this->call_notes,
            'call_direction' => $this->call_direction,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            call_notes: Arr::get($data, 'call_notes'),
            call_direction: Arr::get($data, 'call_direction'),
        );
    }
}
