<?php

namespace App\Http\Resources;

use App\Http\Resources\Tenant\ItemCategory\ItemCategoryDDLResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
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
            'description' => $this->description,
            'price' => $this->price,
            'category' => $this->whenLoaded('category', fn() => new ItemCategoryDDLResource($this->category)),
            'thumbnail_image' => $this->getTenantMediaUrl('uploadThumbnailImage','webp'),
            'images' => $this->getTenantMediaUrls('images','webp'),
            'documents' => $this->getTenantMediaUrls('documents','documents'),
            'itemable_type' => $this->itemable_type,
            'itemable' => $this->itemable_type === 'product' ?
                $this->whenLoaded('itemable', fn() => new ProductResource($this->itemable)) :
                $this->whenLoaded('itemable', fn() => new ServiceResource($this->itemable)),
        ];
    }
}
