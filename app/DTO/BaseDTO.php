<?php

namespace App\DTO;

use App\DTO\Interfaces\DTOInterface;
use Illuminate\Support\Arr;

abstract class BaseDTO implements DTOInterface
{
    public function toArrayExcept(array $except = []): array
    {
        return Arr::except($this->toArray(), $except);
    }

    public function toArrayOnly(array $only = []): array
    {
        return Arr::only($this->toArray(), $only);
    }

    /**
     * Convert the DTO to an array, filtering out null and empty values.
     */
    public function toFilteredArray(): array
    {
        return array_filter($this->toArray());
    }

    /**
     * Convert the DTO to an array, filtering out null and empty values, excluding specified keys.
     */
    public function toFilteredArrayExcept(array $except = []): array
    {
        return Arr::except(array_filter($this->toArray()), $except);
    }
}
