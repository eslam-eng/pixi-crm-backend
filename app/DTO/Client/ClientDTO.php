<?php

namespace App\DTO\Client;

use App\DTO\BaseDTO;
use Illuminate\Support\Arr;

class ClientDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public string $phone,
        public string $email,
        public string $address,
        public int $area_id,
        public ?int $source_id,
        public ?array $serviceCategories = null,
        public ?array $customFields = null,
    ) {}

    public static function fromRequest($request): BaseDTO
    {
        return new self(
            name: $request->input('name'),
            phone: $request->input('phone'),
            email: $request->input('email'),
            address: $request->input('address'),
            area_id: $request->input('area_id'),
            source_id: $request->input('source_id'),
            customFields: $request->input('custom_fields'),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'area_id' => $this->area_id,
            'source_id' => $this->source_id,
            'customFields' => $this->customFields,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: Arr::get($data, 'name'),
            phone: Arr::get($data, 'phone'),
            email: Arr::get($data, 'email'),
            address: Arr::get($data, 'address'),
            area_id: Arr::get($data, 'area_id'),
            source_id: Arr::get($data, 'source_id'),
            customFields: Arr::get($data, 'customFields'),
        );
    }
}
