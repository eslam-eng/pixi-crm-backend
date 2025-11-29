<?php

namespace App\Observers;

use App\Models\Tenant\Task;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        if (auth('api_tenant')->check()) {
            activity()
                ->performedOn($task)
                ->causedBy(user_id())
                ->withProperties([
                    'attributes' => $task->getAttributes()
                ])
                ->useLog('task')
                ->log('task_created');
        }
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        //
    }
}
