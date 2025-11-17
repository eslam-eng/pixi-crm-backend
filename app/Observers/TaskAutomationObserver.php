<?php

namespace App\Observers;

use App\Models\Tenant\Task;
use App\Services\Tenant\Automation\AutomationWorkflowFireService;


class TaskAutomationObserver
{
    public function __construct(
        private AutomationWorkflowFireService $triggerService
    ) {}

    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        // Get the related entity (lead, opportunity, etc.)
        $relatedEntity = null;
        $entityType = null;
        $entityId = null;

        if ($task->lead_id) {
            $relatedEntity = $task->lead;
            $entityType = 'lead';
            $entityId = $task->lead_id;
        }

        $this->triggerService->fireTrigger('task_created', [
            'task' => $task,
            'entity' => $relatedEntity ?? $task,
            'entity_type' => $entityType ?? 'task',
            'entity_id' => $entityId ?? $task->id,
            'assigned_to' => $task->assignedTo,
            'task_type' => $task->taskType,
            'priority' => $task->priority,
        ]);
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        $changes = $task->getChanges();

        // Check if task was completed (status changed to 'completed')
        if (isset($changes['status']) && $changes['status'] === 'completed') {
            // Get the related entity
            $relatedEntity = null;
            $entityType = null;
            $entityId = null;

            if ($task->lead_id) {
                $relatedEntity = $task->lead;
                $entityType = 'lead';
                $entityId = $task->lead_id;
            }

            $this->triggerService->fireTrigger('task_completed', [
                'task' => $task,
                'entity' => $relatedEntity ?? $task,
                'entity_type' => $entityType ?? 'task',
                'entity_id' => $entityId ?? $task->id,
                'completed_at' => now(),
                'assigned_to' => $task->assignedTo,
                'task_type' => $task->taskType,
            ]);
        }
    }
}
