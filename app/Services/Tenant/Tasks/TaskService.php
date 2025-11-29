<?php

namespace App\Services\Tenant\Tasks;

use App\DTO\Tenant\TaskDTO;
use App\Exceptions\GeneralException;
use App\Models\Tenant\Reminder;
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
    ) {}

    public function getModel(): Task
    {
        return $this->model;
    }

    public function getAll(array $filters = [])
    {
        return $this->getQuery($filters)->get();
    }

    public function getQuery(?array $filters = []): ?Builder
    {
        // dd($filters);
        return parent::getQuery($filters)
            ->when(! empty($filters), fn(Builder $builder) => $builder->filter(new TaskFilters($filters)));
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
        
        // Get current counts
        $totalTasks = $this->model->count();
        $completedTasks = $this->model->where('status', 'completed')->count();
        $inProgressTasks = $this->model->where('status', 'in_progress')->count();
        $overdueTasks = $this->model->where('status', '!=', 'completed')
            ->where('due_date', '<', $now->toDateString())
            ->count();

        // Get previous month counts for trend calculation
        $previousTotalTasks = $this->model->where('created_at', '<=', $lastMonth)->count();
        $previousCompletedTasks = $this->model->where('status', 'completed')
            ->where('updated_at', '<=', $lastMonth)
            ->count();
        $previousInProgressTasks = $this->model->where('status', 'in_progress')
            ->where('updated_at', '<=', $lastMonth)
            ->count();
        $previousOverdueTasks = $this->model->where('status', '!=', 'completed')
            ->where('due_date', '<', $lastMonth->toDateString())
            ->count();

        // Calculate percentage changes
        $totalTasksChange = $this->calculatePercentageChange($previousTotalTasks, $totalTasks);
        $completedTasksChange = $this->calculatePercentageChange($previousCompletedTasks, $completedTasks);
        $inProgressTasksChange = $this->calculatePercentageChange($previousInProgressTasks, $inProgressTasks);
        $overdueTasksChange = $this->calculatePercentageChange($previousOverdueTasks, $overdueTasks);

        return [
            'total_tasks' => [
                'value' => $totalTasks,
                'change_percentage' => $totalTasksChange,
                'trend' => $totalTasksChange >= 0 ? 'up' : 'down'
            ],
            'completed' => [
                'value' => $completedTasks,
                'change_percentage' => $completedTasksChange,
                'trend' => $completedTasksChange >= 0 ? 'up' : 'down'
            ],
            'in_progress' => [
                'value' => $inProgressTasks,
                'change_percentage' => $inProgressTasksChange,
                'trend' => $inProgressTasksChange >= 0 ? 'up' : 'down'
            ],
            'overdue' => [
                'value' => $overdueTasks,
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
}
