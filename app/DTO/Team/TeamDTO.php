<?php

namespace App\DTO\Team;

use App\DTO\BaseDTO;
use Illuminate\Support\Arr;

class TeamDTO extends BaseDTO
{
    public function __construct(
        public readonly string $title,
        public readonly ?int $leader_id,
        public readonly ?int $source_id,
        public readonly ?int $location_id,
        public readonly ?array $sales_ids,
    ) {}

    public static function fromRequest($request): TeamDTO
    {
        return new self(
            title: $request->get('title'),
            leader_id: $request->get('leader_id'),
            source_id: $request->get('source_id'),
            location_id: $request->get('location_id'),
            sales_ids: $request->get('sales_ids'),
        );
    }

    /**
     * @param array $data
     * @return $this
     */
    public static function fromArray(array $data): TeamDTO
    {
        return new self(
            title: Arr::get($data, 'title'),
            leader_id: Arr::get($data, 'leader_id'),
            source_id: Arr::get($data, 'source_id'),
            location_id: Arr::get($data, 'location_id'),
            sales_ids: Arr::get($data, 'sales_ids')
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'leader_id' => $this->leader_id,
            'source_id' => $this->source_id,
            'location_id' => $this->location_id,
            'sales_ids' => $this->sales_ids
        ];
    }
}
