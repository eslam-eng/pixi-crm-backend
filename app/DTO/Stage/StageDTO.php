<?php

namespace App\DTO\Stage;

use App\DTO\BaseDTO;
use Illuminate\Support\Arr;

class StageDTO extends BaseDTO
{

    /**
     * @param string $name
     * @param int $probability
     * @param int $pipeline_id
     */
    public function __construct(
        public string $name,
        public int $probability,
    ) {}

    public static function fromRequest($request): StageDTO
    {
        return new self(
            name: $request->name,
            probability: $request->probability,
        );
    }


    /**
     * @param array $data
     * @return $this
     */
    public static function fromArray(array $data): StageDTO
    {
        return new self(
            name: Arr::get($data, 'name'),
            probability: Arr::get($data, 'probability'),
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'probability' => $this->probability,
        ];
    }
}
