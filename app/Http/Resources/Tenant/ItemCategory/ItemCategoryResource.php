<?php

namespace App\Http\Resources\Tenant\ItemCategory;

use App\Http\Resources\Tenant\ItemCategory\ItemCategoryDDLResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property mixed $children
 */
class ItemCategoryResource extends JsonResource
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
            'type' => $this->type,
            'parent_id' => $this->parent_id,
            'children' => $this->whenLoaded('children', fn() => ItemCategoryDDLResource::collection($this->children)),
        ];
    }
}
