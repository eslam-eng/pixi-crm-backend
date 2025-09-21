<?php

namespace App\DTO\Resource;

use App\DTO\BaseDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ResourceDTO extends BaseDTO
{
    /**
     * @param string $name
     * @param mixed $image
     */
    public function __construct(
        protected string $name,
        protected mixed $image = null,
    ) {}

    public static function fromRequest(Request $request): ResourceDTO
    {
        return new self(
            name: $request->name,
            image: $request->image
        );
    }

    /**
     * @param array $data
     * @return $this
     */
    public static function fromArray(array $data): ResourceDTO
    {
        return new self(
            name: Arr::get($data, 'name'),
            image: Arr::get($data, 'image') // Handle image if passed as an array key
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }

    /**
     * @return mixed
     */
    public function getImage(): mixed
    {
        return $this->image;
    }
}
