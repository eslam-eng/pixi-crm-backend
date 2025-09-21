<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LocationCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'pagination' => $this->resource instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator
                ? [
                    'total' => $this->resource->total(),
                    'per_page' => $this->resource->perPage(),
                    'current_page' => $this->resource->currentPage(),
                    'last_page' => $this->resource->lastPage(),
                    'next_page_url' => $this->resource->nextPageUrl(),
                    'prev_page_url' => $this->resource->previousPageUrl(),
                ]
                : null, // No pagination if returning a collection
        ];
    }
}
