<?php

namespace App\DTO\LossReason;

use App\DTO\BaseDTO;
use Illuminate\Support\Arr;

class LossReasonDTO extends BaseDTO
{

    /**
     * @param string $label
     * @param string $value
     * @param string|null $description
     */
    public function __construct(
        public string $label,
        public string $value,
        public string|null $description,
    ) {}

    public static function fromRequest($request): LossReasonDTO
    {
        return new self(
            label: $request->label,
            value: $request->value,
            description: $request->description,
        );
    }


    /**
     * @param array $data
     * @return $this
     */
    public static function fromArray(array $data): LossReasonDTO
    {
        return new self(
            label: Arr::get($data, 'label'),
            value: Arr::get($data, 'value'),
            description: Arr::get($data, 'description'),
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'value' => $this->value,
            'description' => $this->description,
        ];
    }
}
