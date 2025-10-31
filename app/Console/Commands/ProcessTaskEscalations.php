<?php

namespace App\Console\Commands;

use App\Enums\TaskStatusEnum;
use App\Models\Tenant\Task;
use App\Models\Tenant\User;
use App\Notifications\Tenant\TaskEscalationNotification;
use App\Settings\TasksSettings;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessTaskEscalations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:escalate {--tenant=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process task escalations for overdue tasks based on escalation_time_hours setting';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->option('tenant');

        if ($tenantId) {
            // Process escalations for specific tenant
            $this->processTenantEscalations($tenantId);
        } else {
            // Process escalations for all tenants
            $this->processAllTenantsEscalations();
        }
    }

    /**
     * Process escalations for a specific tenant
     */
    private function processTenantEscalations(string $tenantId)
    {
        try {
            // Switch to tenant context
            tenancy()->initialize($tenantId);

            $this->info("Processing task escalations for tenant: {$tenantId}");

            // Get task settings
            $tasksSettings = new TasksSettings();

            $escalationHours = $tasksSettings->escalation_time_hours;
            // Check if escalation is enabled
            if (!$escalationHours) {
                $this->info("Task escalation is disabled for tenant: {$tenantId}");
                return;
            }

            $this->info("Escalation time hours: {$escalationHours}");

            // Find overdue tasks that need escalation
            $overdueTasks = $this->getOverdueTasksForEscalation($escalationHours);

            if ($overdueTasks->isEmpty()) {
                $this->info("No tasks found for escalation in tenant: {$tenantId}");
                return;
            }

            $this->info("Found {$overdueTasks->count()} tasks for escalation");

            // Process each overdue task
            foreach ($overdueTasks as $task) {
                $this->processTaskEscalation($task, $tasksSettings);
            }

            $this->info("Task escalations processed successfully for tenant: {$tenantId}");
        } catch (\Exception $e) {
            $this->error("Error processing task escalations for tenant {$tenantId}: " . $e->getMessage());
            Log::error("Task escalation processing error for tenant {$tenantId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Process escalations for all tenants
     */
    private function processAllTenantsEscalations()
    {
        $tenants = \App\Models\Tenant::all();

        $this->info("Processing task escalations for {$tenants->count()} tenants");

        foreach ($tenants as $tenant) {
            $this->processTenantEscalations($tenant->id);
        }

        $this->info("All tenant task escalations processed");
    }

    /**
     * Get overdue tasks that need escalation
     */
    private function getOverdueTasksForEscalation(int $escalationHours)
    {
        $escalationThreshold = Carbon::now()->subHours($escalationHours);

        return Task::with(['followers', 'assignedTo'])
            ->whereNotIn('status', [TaskStatusEnum::CANCELLED, TaskStatusEnum::COMPLETED])
            ->where('escalation_sent', false) // Only get tasks that haven't had escalation sent yet
            ->where(function ($query) use ($escalationThreshold) {
                $query->where('due_date', '<', $escalationThreshold->toDateString())
                    ->orWhere(function ($subQuery) use ($escalationThreshold) {
                        $subQuery->where('due_date', '=', $escalationThreshold->toDateString())
                            ->where('due_time', '<', $escalationThreshold->toTimeString());
                    });
            })
            ->get();
    }

    /**
     * Process escalation for a single task
     */
    private function processTaskEscalation(Task $task, TasksSettings $tasksSettings)
    {
        try {
            $this->info("Processing escalation for task: {$task->title} (ID: {$task->id})");

            // Get only followers (exclude assigned user)
            $notifyUsers = $task->followers;

            // Check if manager notification is enabled and add managers
            if ($tasksSettings->notify_manager && $task->assignedTo && $task->assignedTo->department_id) {
                $managers = $this->getManagersInDepartment($task->assignedTo->department_id, $task->assigned_to_id);
                if ($managers && $managers->isNotEmpty()) {
                    $notifyUsers = $notifyUsers->merge($managers)->unique('id'); // Remove duplicates
                }
            }

            if ($notifyUsers->isEmpty()) {
                $this->warn("No users to notify for task: {$task->title}");
                // Mark as escalation sent even if no users to notify
                $task->update(['escalation_sent' => true]);
                return;
            }

            $this->info("Notifying {$notifyUsers->count()} users for task: {$task->title}");

            $notificationsSent = 0;
            $notificationsFailed = 0;

            // Send escalation notification to all relevant users
            foreach ($notifyUsers as $user) {
                try {
                    $user->notify(new TaskEscalationNotification($task));
                    $this->line("  - Notification sent to: {$user->first_name} {$user->last_name} ({$user->email})");
                    $notificationsSent++;
                } catch (\Exception $e) {
                    $this->error("  - Failed to notify user {$user->email}: " . $e->getMessage());
                    Log::error("Failed to send escalation notification", [
                        'task_id' => $task->id,
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                    $notificationsFailed++;
                }
            }

            // Mark escalation as sent if at least one notification was sent successfully
            if ($notificationsSent > 0) {
                $task->update(['escalation_sent' => true]);
                $this->info("  - Escalation marked as sent for task: {$task->title}");
            } else {
                $this->warn("  - No notifications sent successfully for task: {$task->title}");
            }
        } catch (\Exception $e) {
            $this->error("Error processing escalation for task {$task->id}: " . $e->getMessage());
            Log::error("Task escalation processing error", [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Get managers in the same department as the assigned user
     */
    private function getManagersInDepartment(int $departmentId, ?int $excludeUserId = null)
    {
        $query = User::where('department_id', $departmentId)
            ->role(\App\Enums\RolesEnum::MANAGER->value);

        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }

        return $query->get();
    }
}
