<?php

namespace App\DTO\ItemCategory;

use App\DTO\BaseDTO;
use Illuminate\Support\Arr;

class ItemCategoryDTO extends BaseDTO
{
    /**
     * @param string $name,
     * @param ?int $parent_id,
     * @param string $type,
     */
    public function __construct(
        protected string $name,
        protected ?int $parent_id,
        protected ?string $type,
    ) {}

    public static function fromRequest($request): BaseDTO
    {
        return new self(
            name: $request->name,
            parent_id: $request->parent_id,
            type: $request->type,
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
            parent_id: Arr::get($data, 'parent_id'),
            type: Arr::get($data, 'type'),
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'parent_id' => $this->parent_id,
            'type' => $this->type,
        ];
    }
}
