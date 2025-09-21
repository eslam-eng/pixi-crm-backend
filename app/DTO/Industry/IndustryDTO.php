<?php

namespace App\DTO\Industry;

use App\DTO\BaseDTO;
use Illuminate\Support\Arr;

class IndustryDTO extends BaseDTO
{

    /**
     * @param string $name',
     */
    public function __construct(
        protected string $name,
    ) {}

    public static function fromRequest($request): BaseDTO
    {
        return new self(
            name: $request->name,
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
}
