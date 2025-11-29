<?php

namespace App\Http\Resources\Tenant\Tasks;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => [
                'id' => $this->priority?->id,
                'name' => $this->priority?->name,
                'level' => $this->priority?->level,
                'hex_code' => $this->priority?->color?->hex_code ?? null,
            ],
            'assigned_to' => [
                'id' => $this->assignedTo?->id,
                'name' => $this->assignedTo?->name,
                'role' => $this->assignedTo?->roles?->first()?->name,
            ],
            'due_date' => Carbon::parse($this->due_date)->format('Y-m-d'),
            'due_time' => Carbon::parse($this->due_time)->format('g:i a'),
            'related_to' => [
                'id' => $this->lead_id,
                'name' => $this->lead?->first()?->description ?? null,
                'type' => $this->lead_id ? 'lead' : null,
            ],
        ];
    }
}
