<?php

namespace App\DTO\PaymentMethod;

use App\DTO\BaseDTO;
use Illuminate\Support\Arr;

class PaymentMethodDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public bool $is_checked = true,
        public bool $is_default = false,
        public bool $is_manual_added = false,
    ) {}

    public static function fromRequest($request): BaseDTO
    {
        return new self(
            name: $request->input('name'),
            is_checked: $request->input('is_checked', true),
            is_default: $request->input('is_default', false),
            is_manual_added: $request->input('is_manual_added', false),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'is_checked' => $this->is_checked,
            'is_default' => $this->is_default,
            'is_manual_added' => $this->is_manual_added,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: Arr::get($data, 'name'),
            is_checked: Arr::get($data, 'is_checked', true),
            is_default: Arr::get($data, 'is_default', false),
            is_manual_added: Arr::get($data, 'is_manual_added', false),
        );
    }
}