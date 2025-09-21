<?php

namespace App\Http\Resources;

use App\Enums\ActivationStatus;
use App\Enums\AvailabilityEnum;
use App\Enums\DurationUnits;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TierResource extends JsonResource
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
            'package_name' => $this->package_name,
            'description' => $this->description,
            'availability' => AvailabilityEnum::from($this->availability)->label(),
            'price' => $this->price,
            'duration' => $this->duration,
            'duration_unit' => DurationUnits::from($this->duration_unit)->label(),
            'max_users' => $this->max_users,
            'max_contacts' => $this->max_contacts,
            'storage_limit' => $this->storage_limit,
            'modules' => TierModuleResource::collection($this->tier_modules),
            'status' =>  ActivationStatus::from($this->status)->label()
        ];
    }
}
