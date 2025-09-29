<?php

namespace App\DTO\Form;

use App\DTO\BaseDTO;
use Illuminate\Support\Arr;

class FormDTO extends BaseDTO
{
    /**
     * @param string $title
     * @param ?string $description
     * @param string $slug
     * @param ?bool $is_active
     * @param ?array $fields
     */
    public function __construct(
        public string $title,
        public ?string $description,
        public string $slug,
        public ?bool $is_active,
        public ?array $fields,
    ) {}

    public static function fromRequest($request): BaseDTO
    {
        return new self(
            title: $request->title,
            description: $request->description,
            slug: $request->slug,
            is_active: $request->is_active,
            fields: $request->fields,
        );
    }

    /**
     * @param array $data
     * @return $this
     */
    public static function fromArray(array $data): BaseDTO
    {
        return new self(
            title: Arr::get($data, 'title'),
            description: Arr::get($data, 'description'),
            slug: Arr::get($data, 'slug'),
            is_active: Arr::get($data, 'is_active'),
            fields: Arr::get($data, 'fields'),
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'slug' => $this->slug,
            'is_active' => $this->is_active,
        ];
    }
}
