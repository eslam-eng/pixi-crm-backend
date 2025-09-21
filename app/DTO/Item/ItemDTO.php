<?php

namespace App\DTO\Item;

use App\DTO\BaseDTO;
use Illuminate\Support\Arr;

class ItemDTO extends BaseDTO
{

    /**
     * @param string $name,
     * @param ?string $description,
     * @param float $price,
     * @param ?int $quantity,
     * @param ?string $sku,
     * @param int $category_id,
     * @param string $type,
     * @param ?string $duration,
     * @param ?string $service_type,
     * @param ?int $quantity,
     */
    public function __construct(
        public string $name,
        public ?string $description,
        public float $price,
        public ?string $sku,
        public int $category_id,
        public string $type,
        public ?string $duration,
        public ?int $quantity,
        public ?string $service_type = null,
    ) {}

    public static function fromRequest($request): BaseDTO
    {
        return new self(
            name: $request->name,
            description: $request->description,
            price: $request->price,
            sku: $request->sku,
            category_id: $request->category_id,
            type: $request->type,
            duration: $request->duration,
            quantity: $request->quantity,
            service_type: $request->service_type ?? null,

        );
    }


    /**
     * @param array $data
     * @return $this
     */
    public static function fromArray(array $data): BaseDTO
    {
        return new self(
            name: Arr::get($data, 'name'),
            description: Arr::get($data, 'description'),
            price: Arr::get($data, 'price'),
            sku: Arr::get($data, 'sku'),
            category_id: Arr::get($data, 'category_id'),
            type: Arr::get($data, 'type'),
            duration: Arr::get($data, 'duration'),
            quantity: Arr::get($data, 'quantity'),
            service_type: Arr::get($data, 'service_type') ?? null,
        );
    }

    /**
     * @return array
     */
    public function toProductArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'sku' => $this->sku,
            'category_id' => $this->category_id,
            'type' => $this->type,
            'stock' => $this->quantity
        ];
    }

    /**
     * @return array
     */
    public function toServiceArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'category_id' => $this->category_id,
            'type' => $this->type,
            'duration' => $this->duration,
            'service_type' => $this->service_type,
        ];
    }

    /**
     * @return array
     */
    public function toArrayVariant(): array
    {
        return [
            'price' => $this->price,
            'stock' => $this->quantity,
            'sku' => $this->sku,
        ];
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'category_id' => $this->category_id,
            'type' => $this->type,
            'duration' => $this->duration,
            'service_type' => $this->service_type,
            'sku' => $this->sku,
            'quantity' => $this->quantity,
        ];
    }
}
