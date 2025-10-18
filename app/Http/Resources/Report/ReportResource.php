<?php

namespace App\Http\Resources\Report;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'report_type' => $this->report_type,
            'category' => $this->category,
            'is_active' => $this->is_active,
            'is_scheduled' => $this->is_scheduled,
            'schedule_frequency' => $this->schedule_frequency,
            'schedule_time' => $this->schedule_time,
            'recipients' => $this->recipients,
            'created_by' => [
                'id' => $this->createdBy?->id,
                'name' => $this->createdBy?->name,
                'email' => $this->createdBy?->email,
            ],
            'last_run_at' => $this->last_run_at?->toISOString(),
            'next_run_at' => $this->next_run_at?->toISOString(),
            'settings' => $this->settings,
            'permissions' => $this->permissions,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'latest_execution' => $this->whenLoaded('latestExecution', function () {
                return new ReportExecutionResource($this->latestExecution->first());
            }),
        ];
    }
}
