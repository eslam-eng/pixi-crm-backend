<?php

namespace App\DTO\CustomField;

use App\DTO\BaseDTO;
use Illuminate\Support\Arr;

class CustomFieldDTO extends BaseDTO
{
    public string $name;
    public string $type;
    public ?array $options;

    public function __construct(string $name, string $type, ?array $options = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->options = $options;
    }

    /**
     * Create a DTO from a request array.
     */
    public static function fromRequest($request): self
    {
        return new self(
            name: $request->name,
            type: $request->type,
            options: $request->options?? [],
        );
    }

    /**
     * Create a DTO from an array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: Arr::get($data, 'name'),
            type: Arr::get($data, 'type'),
            options: Arr::get($data, 'options'),
        );
    }

    /**
     * Convert the DTO to an array.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'options' => $this->options,
        ];
    }
}
