<?php

namespace App\Http\Resources\Opportunity;

use App\Http\Resources\ContactResource;
use App\Http\Resources\ItemProductVariantResource;
use App\Http\Resources\ItemResource;
use App\Http\Resources\StageResource;
use App\Http\Resources\Tenant\Items\ItemPovitResource;
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

        // dd($this->variants);
        return [
            'id' => $this->id,
            'status' => $this->status,
            'deal_value' => $this->deal_value,
            'win_probability' => $this->win_probability,
            'expected_close_date' => $this->expected_close_date,
            'assigned_to' => $this->whenLoaded('user', fn() => new UserResource($this->user)),
            'notes' => $this->notes,
            'description' => $this->description,
            'contact' => $this->whenLoaded('contact', fn() => new ContactResource($this->contact)),
            'stage' => $this->whenLoaded('stage', fn() => new StageResource($this->stage)),
            'items' => $this->whenLoaded('items', fn() => ItemResource::collection($this->items)),
        ];
    }
}
