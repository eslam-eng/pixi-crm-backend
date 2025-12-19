<?php

namespace App\DTO\Item;

use App\DTO\BaseDTO;
use Illuminate\Support\Arr;

class ItemDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public ?string $description,
        public float $price,
        public int $category_id,
        public ?string $thumbnail_image = null,
        public ?array $images = null,
        public ?array $documents = null,
    ) {}

    public static function fromRequest($request): BaseDTO
    {
        return new self(
            name: $request->name,
            description: $request->description,
            price: $request->price,
            category_id: $request->category_id,
            thumbnail_image: $request->thumbnail_image,
            images : $request->images, // Multiple images
            documents : $request->documents,
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
            thumbnail_image: Arr::get($data, 'thumbnail_image'),
            images: Arr::get($data, 'images'),
            documents: Arr::get($data, 'documents'),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'category_id' => $this->category_id,
            'thumbnail_image' => $this->thumbnail_image,
            'images' => $this->images,
            'documents' => $this->documents
        ];
    }
}
