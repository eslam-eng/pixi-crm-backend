<?php

namespace App\DTO\Item;

use App\DTO\BaseDTO;
use Illuminate\Support\Arr;

class ServiceDTO extends BaseDTO
{
    /**
     * @param ?string $duration,
     * @param ?string $service_type,
     * @param ?bool $is_recurring,
     */
    public function __construct(
        public ?string $duration,
        public ?bool $is_recurring,
        public ?string $service_type,
    ) {}

    public static function fromRequest($request): BaseDTO
    {
        return new self(
            duration: $request->duration,
            service_type: $request->service_type,
            is_recurring: $request->is_recurring,
        );
    }

    /**
     * @param array $data
     * @return $this
     */
    public static function fromArray(array $data): BaseDTO
    {
        return new self(
            duration: Arr::get($data, 'duration'),
            service_type: Arr::get($data, 'service_type'),
            is_recurring: Arr::get($data, 'is_recurring'),
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'duration' => $this->duration,
            'service_type' => $this->service_type,
            'is_recurring' => $this->is_recurring,
        ];
    }
}
