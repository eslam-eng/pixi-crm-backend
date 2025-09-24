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
     * @param int $category_id,
     */
    public function __construct(
        public string $name,
        public ?string $description,
        public float $price,
        public int $category_id,
    ) {}

    public static function fromRequest($request): BaseDTO
    {
        return new self(
            name: $request->name,
            description: $request->description,
            price: $request->price,
            category_id: $request->category_id,
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
            category_id: Arr::get($data, 'category_id'),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'category_id' => $this->category_id,
        ];
    }
}
