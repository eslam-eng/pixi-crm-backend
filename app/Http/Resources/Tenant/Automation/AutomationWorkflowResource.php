<?php

namespace App\Http\Resources\Tenant\Automation;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AutomationWorkflowResource extends JsonResource
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
            'is_active' => $this->is_active,
            'automation_trigger' => [
                'id' => $this->automationTrigger->id,
                'key' => $this->automationTrigger->key,
                'name' => $this->automationTrigger->name,
                'icon' => $this->automationTrigger->icon,
            ],
            'steps_count' => $this->steps->count(),
            'total_runs' => $this->total_runs,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

}
