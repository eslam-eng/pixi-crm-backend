<?php

namespace App\Http\Resources\Opportunity;

use App\Http\Resources\ContactResource;
use App\Http\Resources\Opportunity\ItemInOpportunity;
use App\Http\Resources\ItemResource;
use App\Http\Resources\StageResource;
use App\Http\Resources\Tenant\Users\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OpportunityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Merge items and variants into a single items_details array
        $items = $this->relationLoaded('items')
            ? collect(ItemInOpportunity::collection($this->items)->resolve())->map(function ($item) {
                return [
                    'item_id' => $item['id'],
                    'variant_id' => null,
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'category_id' => $item['category_id'],
                    'category_name' => $item['category_name'],
                    'sub_category_id' => $item['sub_category_id'],
                    'sub_category_name' => $item['sub_category_name'],
                    'service_type' => $item['service_type'],
                    'type' => $item['type'],
                ];
            })->values()->all()
            : [];

        $variants = $this->relationLoaded('variants')
            ? $this->variants->filter()->map(function ($variant) {
                return [
                    'item_id' => $variant->product->item->id ?? null,
                    'variant_id' => $variant->id,
                    'name' => $variant->product->item->name ?? 'Unknown Variant',
                    'price' => $variant->pivot->price ?? 0,
                    'quantity' => $variant->pivot->quantity ?? 0,
                    'category_id' => $variant->product->item->category->parent->id,
                    'category_name' => $variant->product->item->category->parent->name,
                    'sub_category_id' => $variant->product->item->category->id,
                    'sub_category_name' => $variant->product->item->category->name,
                    'service_type' => null,
                    'type' => $variant->product->item->itemable_type,
                ];
            })->values()->all()
            : [];

        $mergedItemsDetails = array_merge($items, $variants);

        return [
            'id' => $this->id,
            'status' => $this->status,
            'is_qualifying' => $this->is_qualifying,
            'deal_value' => $this->deal_value,
            'win_probability' => $this->win_probability,
            'expected_close_date' => $this->expected_close_date,
            'assigned_to' => $this->whenLoaded('user', fn() => new UserResource($this->user)),
            'notes' => $this->notes,
            'description' => $this->description,
            'contact' => $this->whenLoaded('contact', fn() => new ContactResource($this->contact)),
            'stage' => $this->whenLoaded('stage', fn() => new StageResource($this->stage)),
            // 'items' => $this->whenLoaded('items', fn() => ItemResource::collection($this->items)),
            'items_details' => $mergedItemsDetails,
        ];
    }
}
