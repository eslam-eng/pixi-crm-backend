<?php

namespace App\Http\Resources\Tenant\Opportunity;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OpportunityDDLResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Merge items and variants into a single items_details array
        $items = $this->relationLoaded('items')
            ? $this->items->filter()->map(function ($item) {
                return [
                    'item_id' => $item['id'],
                    'name' => $item->name,
                    'price' => $item->price,
                    'quantity' => $item->pivot->quantity,
                    'category_id' => $item->category->id,
                    'sub_category_id' => $item->category->parent->id,
                    'service_type' => $item->service?->service_type,
                    'type' => $item->itemable_type,
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
                    'sub_category_id' => $variant->product->item->category->id,
                    'service_type' => null,
                    'type' => $variant->product->item->itemable_type,
                ];
            })->values()->all()
            : [];

        $mergedItemsDetails = array_merge($items, $variants);


        return [
            'id' => $this->id,
            'contact' => $this->whenLoaded('contact', fn() => $this->contact->first_name . ' ' . $this->contact->last_name),
            'description' => $this->description,
            'items_details' => $mergedItemsDetails,
        ];
    }
}
