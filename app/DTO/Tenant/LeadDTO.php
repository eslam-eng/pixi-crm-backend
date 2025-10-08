<?php

namespace App\DTO\Tenant;

use App\DTO\BaseDTO;
use Illuminate\Support\Arr;

class LeadDTO extends BaseDTO
{
    public function __construct(
        public ?int $contact_id,
        public ?string $status,
        public ?int $stage_id,
        public ?bool $is_qualifying,
        public ?float $deal_value,
        public ?float $win_probability,
        public ?string $expected_close_date,
        public ?int $assigned_to_id,
        public ?string $notes,
        public ?string $description,
        public ?array $items,
    ) {}

    public static function fromRequest($request): BaseDTO
    {
        return new self(
            contact_id: $request->contact_id,
            status: $request->status,
            stage_id: $request->stage_id,
            is_qualifying: $request->is_qualifying,
            deal_value: $request->deal_value,
            win_probability: $request->win_probability,
            expected_close_date: $request->expected_close_date,
            assigned_to_id: $request->assigned_to_id,
            notes: $request->notes,
            description: $request->description,
            items: $request->items,
        );
    }

    public function toArray(): array
    {
        $data = [
            'contact_id' => $this->contact_id,
            'assigned_to_id' => $this->assigned_to_id,
            'notes' => $this->notes,
            'description' => $this->description,
            'stage_id' => $this->stage_id,
            'is_qualifying' => $this->is_qualifying,
            'deal_value' => $this->deal_value,
            'win_probability' => $this->win_probability,
            'expected_close_date' => $this->expected_close_date,
            'status' => $this->status,
        ];

        // Fields that are allowed to be null in database
        $nullableFields = ['notes', 'description', 'expected_close_date'];

        return array_filter($data, function ($value, $key) use ($nullableFields) {
            return in_array($key, $nullableFields) || !is_null($value);
        }, ARRAY_FILTER_USE_BOTH);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            contact_id: Arr::get($data, 'contact_id'),
            stage_id: Arr::get($data, 'stage_id'),
            assigned_to_id: Arr::get($data, 'assigned_to_id'),
            notes: Arr::get($data, 'notes'),
            description: Arr::get($data, 'description'),
            is_qualifying: Arr::get($data, 'is_qualifying'),
            deal_value: Arr::get($data, 'deal_value'),
            win_probability: Arr::get($data, 'win_probability'),
            expected_close_date: Arr::get($data, 'expected_close_date'),
            status: Arr::get($data, 'status'),
            items: Arr::get($data, 'items'),
        );
    }
}
