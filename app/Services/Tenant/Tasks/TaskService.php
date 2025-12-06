<?php

namespace App\Services\Tenant\Tasks;

use App\DTO\Tenant\TaskDTO;
use App\Exceptions\GeneralException;
use App\Models\Tenant\Reminder;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Tenant\Task;
use App\QueryFilters\Tenant\TaskFilters;
use App\Services\BaseService;
use DB;
use Illuminate\Contracts\Pagination\CursorPaginator;

class TaskService extends BaseService
{
    public function __construct(
        public Task $model,
    ) {
    }

    public function getModel(): Task
    {
        return $this->model;
    }

    public function getAll(array $filters = [], ?array $withRelations = [])
    {
        return $this->getQuery($filters)->with($withRelations)->get();
    }

    public function getQuery(?array $filters = []): ?Builder
    {
        // dd($filters);
        return parent::getQuery($filters)
            ->when(!empty($filters), fn(Builder $builder) => $builder->filter(new TaskFilters($filters)));
    }

    public function paginate(?array $filters = [], ?array $withRelations = [], int $limit = 10): CursorPaginator
    {
        return $this->getQuery($filters)
            ->with($withRelations)
            ->ordered()
            ->cursorPaginate($limit);
    }

    public function store(TaskDTO $taskDTO): Task
    {
        try {
            DB::beginTransaction();

            // Create the task
            $data = $this->model->create($taskDTO->toArray());

            // Save followers if provided
            if (!empty($taskDTO->followers)) {
                $this->syncFollowers($data, $taskDTO->followers);
            }

            // Save reminders if provided
            if (!empty($taskDTO->reminders)) {
                $this->syncReminders($data, $taskDTO->reminders);
            }

            DB::commit();
            return $data;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new GeneralException('Failed to create task: ' . $e->getMessage());
        }
    }

    public function update(int $id, TaskDTO $taskDTO): Task
    {
        try {
            DB::beginTransaction();

            // Find the task
            $task = $this->findById($id);

            // Update the task
            $task->update($taskDTO->toArray());

            // Sync followers if provided
            if (isset($taskDTO->followers)) {
                if (!empty($taskDTO->followers)) {
                    $this->syncFollowers($task, $taskDTO->followers);
                } else {
                    // If empty array provided, detach all followers
                    $task->followers()->detach();
                }
            }

            // Sync reminders if provided
            if (isset($taskDTO->reminders)) {
                if (!empty($taskDTO->reminders)) {
                    $this->syncReminders($task, $taskDTO->reminders);
                } else {
                    // If empty array provided, detach all reminders
                    $task->reminders()->detach();
                }
            }

            DB::commit();
            return $task->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new GeneralException('Failed to update task: ' . $e->getMessage());
        }
    }

    /**
     * Sync followers for a task
     */
    public function syncFollowers(Task $task, array $followerIds): void
    {
        // Handle both array format [['id' => 1], ['id' => 2]] and simple format [1, 2]
        $ids = [];
        foreach ($followerIds as $follower) {
            $ids[] = is_array($follower) ? ($follower['id'] ?? $follower[0] ?? $follower) : $follower;
        }
        $task->followers()->sync($ids);
    }

    /**
     * Sync reminders for a task
     */
    public function syncReminders(Task $task, array $reminderIds): void
    {
        // First, detach all existing reminders
        $task->reminders()->detach();

        // Then add the new reminders
        $reminders = Reminder::whereIn('id', $reminderIds)->get();

        foreach ($reminders as $reminder) {
            $task->addReminder($reminder);
        }
    }

    /**
     * Add a follower to a task
     */
    public function addFollower(Task $task, int $followerId): void
    {
        $task->followers()->syncWithoutDetaching([$followerId]);
    }

    /**
     * Remove a follower from a task
     */
    public function removeFollower(Task $task, int $followerId): void
    {
        $task->followers()->detach($followerId);
    }

    /**
     * Get task with followers
     */
    public function getWithFollowers(int $taskId): Task
    {
        return $this->model->with('followers')->findOrFail($taskId);
    }

    /**
     * Change task status
     */
    public function changeStatus(int $id, string $status): Task
    {
        try {
            DB::beginTransaction();

            $task = $this->findById($id);
            $task->update(['status' => $status]);

            DB::commit();
            return $task->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new GeneralException('Failed to change task status: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): bool
    {
        try {
            DB::beginTransaction();

            $task = $this->findById($id);

            // Detach followers before deleting
            $task->followers()->detach();

            // Delete any associated reminders
            $task->reminders()->detach();

            $result = $task->delete();

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new GeneralException('Failed to delete task: ' . $e->getMessage());
        }
    }

    /**
     * Get task statistics
     */
    public function getStatistics(): array
    {
        $now = now();
        $lastMonth = $now->copy()->subMonth();

        // Use single query with conditional aggregation for better performance
        $stats = \DB::table('tasks')
            ->selectRaw('
                COUNT(*) as total_tasks,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_tasks,
                SUM(CASE WHEN status = "in_progress" THEN 1 ELSE 0 END) as in_progress_tasks,
                SUM(CASE WHEN status != "completed" AND due_date < ? THEN 1 ELSE 0 END) as overdue_tasks,
                SUM(CASE WHEN created_at <= ? THEN 1 ELSE 0 END) as previous_total_tasks,
                SUM(CASE WHEN status = "completed" AND updated_at <= ? THEN 1 ELSE 0 END) as previous_completed_tasks,
                SUM(CASE WHEN status = "in_progress" AND updated_at <= ? THEN 1 ELSE 0 END) as previous_in_progress_tasks,
                SUM(CASE WHEN status != "completed" AND due_date < ? THEN 1 ELSE 0 END) as previous_overdue_tasks
            ', [
                $now->toDateString(),
                $lastMonth,
                $lastMonth,
                $lastMonth,
                $lastMonth->toDateString()
            ])
            ->first();

        // Calculate percentage changes
        $totalTasksChange = $this->calculatePercentageChange($stats->previous_total_tasks, $stats->total_tasks);
        $completedTasksChange = $this->calculatePercentageChange($stats->previous_completed_tasks, $stats->completed_tasks);
        $inProgressTasksChange = $this->calculatePercentageChange($stats->previous_in_progress_tasks, $stats->in_progress_tasks);
        $overdueTasksChange = $this->calculatePercentageChange($stats->previous_overdue_tasks, $stats->overdue_tasks);

        return [
            'total_tasks' => [
                'value' => $stats->total_tasks,
                'change_percentage' => $totalTasksChange,
                'trend' => $totalTasksChange >= 0 ? 'up' : 'down'
            ],
            'completed' => [
                'value' => $stats->completed_tasks,
                'change_percentage' => $completedTasksChange,
                'trend' => $completedTasksChange >= 0 ? 'up' : 'down'
            ],
            'in_progress' => [
                'value' => $stats->in_progress_tasks,
                'change_percentage' => $inProgressTasksChange,
                'trend' => $inProgressTasksChange >= 0 ? 'up' : 'down'
            ],
            'overdue' => [
                'value' => $stats->overdue_tasks,
                'change_percentage' => $overdueTasksChange,
                'trend' => $overdueTasksChange >= 0 ? 'up' : 'down'
            ]
        ];
    }

    /**
     * Calculate percentage change between two values
     */
    private function calculatePercentageChange(int $oldValue, int $newValue): float
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }

        return round((($newValue - $oldValue) / $oldValue) * 100, 1);
    }

    public function tasksCount()
    {
        $filters['dashboard_view'] = Auth::user();

        $count = $this->getQuery($filters)->count();
        return $count;
    }

}
