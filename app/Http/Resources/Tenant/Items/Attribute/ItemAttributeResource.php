<?php

namespace App\Http\Resources\Tenant\Items\Attribute;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ItemAttributeResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'values' => $this->whenLoaded('values', fn() => ItemAttributeValueResource::collection($this->values)),
        ];
    }
}
