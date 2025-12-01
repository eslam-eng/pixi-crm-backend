<?php

namespace App\DTO\Tenant\Opportunity;

use App\DTO\BaseDTO;
use Illuminate\Support\Arr;

class ActivityLogDTO extends BaseDTO
{
    public function __construct(
        public string $activity_type,
        public string $title,
        public ?string $description,
    ) {}

    public static function fromRequest($request): BaseDTO
    {
        return new self(
            activity_type: $request->input('activity_type'),
            title: $request->input('title'),
            description: $request->input('description'),
        );
    }

    public function toArray(): array
    {
        return [
            'activity_type' => $this->activity_type,
            'title' => $this->title,
            'description' => $this->description,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            activity_type: Arr::get($data, 'activity_type'),
            title: Arr::get($data, 'title'),
            description: Arr::get($data, 'description'),
        );
    }
}
