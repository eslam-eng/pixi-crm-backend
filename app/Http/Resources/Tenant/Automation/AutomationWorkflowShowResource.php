<?php

namespace App\Http\Resources\Tenant\Automation;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AutomationWorkflowShowResource extends JsonResource
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
            'total_runs' => $this->total_runs,
            'steps' => $this->steps->map(function ($step) {
                return [
                    'id' => $step->id,
                    'type' => $step->type,
                    'order' => $step->order,
                    'data' => $this->formatStepData($step),
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Format step data based on type
     */
    private function formatStepData($step): array
    {
        switch ($step->type) {
            case 'condition':
                return $step->condition ? [
                    'field' => $step->condition->field,
                    'operation' => $step->condition->operation,
                    'value' => $step->condition->value,
                ] : [];

            case 'action':
                return $step->action ? [
                    'automation_action_id' => $step->action->automation_action_id,
                    'automation_action' => [
                        'id' => $step->action->automationAction->id,
                        'key' => $step->action->automationAction->key,
                        'name' => $step->action->automationAction->name,
                        'icon' => $step->action->automationAction->icon,
                    ],
                ] : [];

            case 'delay':
                return $step->delay ? [
                    'duration' => $step->delay->duration,
                    'unit' => $step->delay->unit,
                ] : [];

            default:
                return [];
        }
    }
}
