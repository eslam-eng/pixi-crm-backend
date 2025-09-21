<?php

namespace App\Services;

use App\Models\Tenant\Task;
use App\Models\Tenant\Lead;
use App\Services\Tenant\Tasks\TaskService;
use Exception;

class CoreService extends BaseService
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
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
            $taskStatistics = $this->taskService->getStatistics();
            
            // Get opportunity counts
            $opportunityCounts = $this->getOpportunityCounts();
            
            return [
                'tasks' => $taskStatistics['total_tasks']['value'],
                'opportunities' => $opportunityCounts
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to get sidebar counts: ' . $e->getMessage());
        }
    }

    /**
     * Get opportunity counts
     */
    private function getOpportunityCounts()
    {
        try {
            $totalOpportunities = Lead::count();
        
            return $totalOpportunities;
        } catch (Exception $e) {
            throw new Exception('Failed to get opportunity counts: ' . $e->getMessage());
        }
    }
}