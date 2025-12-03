<?php

namespace App\Services\Tenant\Tasks;

use App\Models\Tenant\Reminder;
use App\Models\Tenant\Task;
use App\Models\TaskReminder;
use App\Mail\TaskReminderMail;
use App\Notifications\Tenant\TaskReminderNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class ReminderService
{

    public function __construct(
        public Reminder $model,
    ) {
    }

    public function getModel(): Reminder
    {
        return $this->model;
    }

    public function getAll(array $filters = [])
    {
        return $this->queryGet($filters)->get();
    }

    public function getTableName(): string
    {
        return $this->getModel()->getTable();
    }

    public function listing(array $filters = [], array $withRelations = [], $perPage = 5): \Illuminate\Contracts\Pagination\CursorPaginator
    {
        return $this->queryGet(filters: $filters, withRelations: $withRelations)->cursorPaginate($perPage);
    }

    public function queryGet(array $filters = [], array $withRelations = []): Builder
    {
        $data = $this->model->with($withRelations)->ordered();
        return $data;
    }


    /**
     * Get reminder by ID
     */
    public function getReminderById(int $id): ?Reminder
    {
        return Reminder::find($id);
    }

    /**
     * Calculate reminder time for a given task due date
     */
    public function calculateReminderTime(Carbon $taskDueDate, Reminder $reminder): Carbon
    {
        if ($reminder->time_unit === 'on_time') {
            return $taskDueDate;
        }

        return $taskDueDate->subMinutes($reminder->total_minutes);
    }

    /**
     * Get reminders that should be triggered for tasks due within a specific time range
     */
    public function getRemindersForTimeRange(Carbon $startTime, Carbon $endTime)
    {
        $reminders = $this->getAll();
        $applicableReminders = [];

        foreach ($reminders as $reminder) {
            if ($reminder->time_unit === 'on_time') {
                // For "on time" reminders, check if current time is within the range
                if ($startTime->lte(now()) && $endTime->gte(now())) {
                    $applicableReminders[] = $reminder;
                }
            } else {
                // For other reminders, check if the reminder time falls within the range
                $reminderTime = now()->addMinutes($reminder->total_minutes);
                if ($reminderTime->between($startTime, $endTime)) {
                    $applicableReminders[] = $reminder;
                }
            }
        }

        return $applicableReminders;
    }

    /**
     * Get tasks that need reminders based on reminder settings
     */
    public function getTasksForReminder(Reminder $reminder)
    {
        $now = now();

        return Task::whereHas('taskReminders', function ($query) use ($reminder, $now) {
            $query->where('is_sent', false)
                ->whereNotNull('reminder_at')
                ->where('reminder_id', $reminder->id)
                ->where('reminder_at', '<=', $now);
        })
            ->with([
                'assignedTo',
                'priority',
                'taskReminders' => function ($query) use ($reminder, $now) {
                    $query->where('is_sent', false)
                        ->whereNotNull('reminder_at')
                        ->where('reminder_id', $reminder->id)
                        ->where('reminder_at', '<=', $now);
                }
            ])
            ->get();
    }

    /**
     * Send reminder notification
     */
    public function sendReminderNotification(Task $task, Reminder $reminder)
    {
        try {
            // Check if the task has an assigned user
            if (!$task->assignedTo) {
                Log::warning("Task {$task->id} has no assigned user, skipping reminder");
                return;
            }

            $assignedUser = $task->assignedTo;
            $userName = $assignedUser->name ?? $assignedUser->first_name . ' ' . $assignedUser->last_name;

            // Send email notification
            // $this->sendEmailReminder($task, $reminder, $assignedUser, $userName);

            // Send in-app notification
            $this->sendInAppNotification($task, $reminder, $assignedUser);

            // Mark the reminder as sent
            $this->markReminderAsSent($task, $reminder);

            Log::info("Reminder sent successfully for task {$task->id} to user {$assignedUser->id}");

        } catch (\Exception $e) {
            Log::error("Failed to send reminder for task {$task->id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Send email reminder
     */
    private function sendEmailReminder(Task $task, Reminder $reminder, $user, string $userName)
    {
        try {
            Mail::to($user->email)->send(new TaskReminderMail($task, $reminder, $userName));
            Log::info("Email reminder sent to {$user->email} for task {$task->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send email reminder to {$user->email} for task {$task->id}", [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send in-app notification
     */
    private function sendInAppNotification(Task $task, Reminder $reminder, $user)
    {
        try {
            $user->notify(new TaskReminderNotification($task, $reminder));
            Log::info("In-app notification sent to user {$user->id} for task {$task->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send in-app notification to user {$user->id} for task {$task->id}", [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Mark reminder as sent
     */
    private function markReminderAsSent(Task $task, Reminder $reminder)
    {
        TaskReminder::where('task_id', $task->id)
            ->where('reminder_id', $reminder->id)
            ->update([
                'is_sent' => true,
                'sent_at' => now()
            ]);
    }

    /**
     * Process all pending reminders
     */
    public function processPendingReminders()
    {
        // 1) Always process backlog: any unsent reminders whose reminder_at is now or in the past
        $this->processOverdueUnsentTaskReminders();

        // 2) Then process near-future window similarly to previous behavior (next 60 minutes)
        $now = now();
        $nextHour = $now->copy()->addHour();
        $reminders = $this->getRemindersForTimeRange($now, $nextHour);

        foreach ($reminders as $reminder) {
            $tasks = $this->getTasksForReminder($reminder);
            foreach ($tasks as $task) {
                $this->sendReminderNotification($task, $reminder);
            }
        }
    }

    /**
     * Get reminder statistics for dashboard
     */
    public function getReminderStats()
    {
        return [
            'total_reminders' => Reminder::count(),
            'reminders_by_unit' => Reminder::selectRaw('time_unit, COUNT(*) as count')
                ->groupBy('time_unit')
                ->get()
                ->pluck('count', 'time_unit')
                ->toArray(),
        ];
    }

    /**
     * Process all unsent task_reminders whose reminder_at <= now (backlog and due).
     */
    private function processOverdueUnsentTaskReminders(): void
    {
        try {
            $dueTaskReminders = TaskReminder::with(['task.assignedTo', 'reminder'])
                ->where('is_sent', false)
                ->whereNotNull('reminder_at')
                ->where('reminder_at', '<=', now())
                ->get();

            if ($dueTaskReminders->isEmpty()) {
                //No overdue unsent task reminders found.
                return;
            }

            Log::info('Processing ' . $dueTaskReminders->count() . ' overdue unsent task reminders.');

            foreach ($dueTaskReminders as $taskReminder) {
                $task = $taskReminder->task;
                $reminder = $taskReminder->reminder;

                if (!$task || !$reminder) {
                    continue;
                }

                if (!$task->assignedTo) {
                    Log::warning("Task {$task->id} has no assigned user, skipping overdue reminder");
                    // Still mark as sent to avoid infinite retries with no recipient
                    $this->markReminderAsSent($task, $reminder);
                    continue;
                }

                $this->sendReminderNotification($task, $reminder);
            }
        } catch (\Exception $e) {
            Log::error('Failed processing overdue unsent task reminders', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}