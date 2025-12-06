<?php

namespace App\Services;

use App\Models\Tenant\Task;
use App\Models\Tenant\Lead;
use App\Services\Tenant\Tasks\TaskService;
use Exception;

class CoreService extends BaseService
{
    protected $taskService;
    protected $leadService;

    public function __construct(TaskService $taskService, LeadService $leadService)
    {
        $this->taskService = $taskService;
        $this->leadService = $leadService;
    }

    /**
     * Get model for base service
     */
    public function getModel(): \Illuminate\Database\Eloquent\Model
    {
        // This service doesn't have a single model, it aggregates data
        // Return a dummy model or throw an exception
        throw new \RuntimeException('CoreService does not have a single model');
    }

    /**
     * Get sidebar counts for tasks and opportunities
     */
    public function getSidebarCounts(): array
    {
        try {
            // Get task counts using TaskService
            $tasksCount = $this->taskService->tasksCount();

            // Get opportunity counts
            $opportunitiesCount = $this->leadService->opportunityCount();

            return [
                'tasks' => $tasksCount,
                'opportunities' => $opportunitiesCount
            ];

        } catch (Exception $e) {
            throw new Exception('Failed to get sidebar counts: ' . $e->getMessage());
        }
    }

}