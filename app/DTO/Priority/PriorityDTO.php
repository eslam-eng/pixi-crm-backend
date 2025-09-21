<?php

namespace App\DTO\Priority;

use App\DTO\BaseDTO;
use Illuminate\Support\Arr;

class PriorityDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public int $color_id,
        public int $level,
        public bool $is_default = false,
    ) {}

    public static function fromRequest($request): BaseDTO
    {
        return new self(
            name: $request->input('name'),
            color_id: $request->input('color_id'),
            level: $request->input('level'),
            is_default: $request->input('is_default', false),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'color_id' => $this->color_id,
            'level' => $this->level,
            'is_default' => $this->is_default,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: Arr::get($data, 'name'),
            color_id: Arr::get($data, 'color_id'),
            level: Arr::get($data, 'level'),
            is_default: Arr::get($data, 'is_default', false),
        );
    }
}