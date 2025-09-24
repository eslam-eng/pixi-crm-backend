<?php

namespace App\DTO\Item;

use App\DTO\BaseDTO;
use Illuminate\Support\Arr;

class ProductDTO extends BaseDTO
{
    /**
     * @param int $stock,
     * @param ?string $sku,
     * 
     */
    public function __construct(
        public int $stock,
        public ?string $sku,
        public ?array $variants,
    ) {}

    public static function fromRequest($request): BaseDTO
    {
        return new self(
            stock: $request->stock,
            sku: $request->sku,
            variants: $request->variants,
        );
    }


    /**
     * @param array $data
     * @return $this
     */
    public static function fromArray(array $data): BaseDTO
    {
        return new self(
            stock: Arr::get($data, 'stock'),
            sku: Arr::get($data, 'sku'),
            variants: Arr::get($data, 'variants'),
        );
    }

    /**
     * @return array
     */
    public function toProductArray(): array
    {
        return [
            'stock' => $this->stock,
            'sku' => $this->sku,
            'variants' => $this->variants,
        ];
    }

    public function toArray(): array
    {
        return [
            'stock' => $this->stock,
            'sku' => $this->sku,
            'variants' => $this->variants,
        ];
    }
}
