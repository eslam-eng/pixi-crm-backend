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
        $this->triggerService->fireTrigger('task_created', [
            'triggerable_type' => get_class($task),
            'triggerable_id' => $task->id,
            'task' => $task,
            'entity' => $task,
            'entity_type' => 'task',
            'entity_id' => $task->id,
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
            $this->triggerService->fireTrigger('task_completed', [
                'triggerable_type' => get_class($task),
                'triggerable_id' => $task->id,
                'task' => $task,
                'entity' => $task,
                'entity_type' => 'task',
                'entity_id' => $task->id,
                'completed_at' => now(),
                'assigned_to' => $task->assignedTo,
                'task_type' => $task->taskType,
            ]);
        }
    }
}
