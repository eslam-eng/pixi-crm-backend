<?php

namespace App\Http\Resources\Tenant;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property mixed $area
 * @property mixed $source
 */
class TenantResource extends JsonResource
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
            'business_name' => $this->name,
            'start_date' => $this->created_at,
            'login url' => $this->domains->first()?->domain ? 'https://' . $this->domains->first()?->domain . "." . env('APP_URL') : null,
            // 'owner' => $this->whenLoaded('user', fn() =>  new LandlordUserResource($this->user)),
            'business_logo' => $this->getFirstMediaUrl('images'),
        ];
    }
}
