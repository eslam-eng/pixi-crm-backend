<?php

namespace App\Http\Resources\Tenant\Integration;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IntegratedFormResource extends JsonResource
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
            'form_name' => $this->form_name,
            'external_form_id' => $this->external_form_id,
            'status' => $this->is_active ? 'active' : 'paused',
            'platform' => $this->platform->value,
            'platform_label' => $this->platform->label(),
            'sync_info' => [
                'last_sync' => $this->updated_at ? $this->updated_at->format('n/j/Y, g:i:s A') : null,
                'total_leads' => $this->total_contacts_count ?? 0,
            ],
            'field_mappings' => [
                'count' => $this->whenLoaded('fieldMappings', function () {
                    return $this->fieldMappings->count();
                }),
                'mappings' => $this->whenLoaded('fieldMappings', function () {
                    return $this->fieldMappings->map(function ($mapping) {
                        return [
                            'external_field' => $mapping->external_field_key,
                            'crm_field' => $mapping->contact_column,
                            'is_required' => $mapping->is_required,
                        ];
                    })->toArray();
                }),
            ],
        ];
    }
}
