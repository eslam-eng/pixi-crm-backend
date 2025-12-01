<?php

namespace App\DTO\Item;

use App\DTO\BaseDTO;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

class ItemDTO extends BaseDTO
{
    /**
     * @param string $name,
     * @param ?string $description,
     * @param float $price,
     * @param int $category_id,
     * @param uploadedFile $thumbnail_image
     * @param ?array $images
     * @param ?array $documents
    */
    public function __construct(
        public string $name,
        public ?string $description,
        public float $price,
        public int $category_id,
        public ?UploadedFile $thumbnail_image = null,
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
            thumbnail_image: $request->file('thumbnail_image'),
            images : $request->file('images'), // Multiple images
            documents : $request->file('documents'),
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

    /**
    * Check if has avatar file
    */
    public function hasThumbnailImage(): bool
    {
        return $this->thumbnail_image instanceof UploadedFile;
    }

    /**
    * Check if has multiple images
    */
    public function hasImages(): bool
    {
        return is_array($this->images) && count($this->images) > 0;
    }

    /**
    * Check if has documents
    */
    public function hasDocuments(): bool
    {
        return is_array($this->documents) && count($this->documents) > 0;
    }
}
