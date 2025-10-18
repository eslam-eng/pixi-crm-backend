<?php

namespace App\Http\Controllers\Api\Report;

use App\DTO\Report\TaskManagementReportDTO;
use App\Http\Controllers\Controller;
use App\Services\Report\ReportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskManagementController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    /**
     * Get task completion report
     */
    public function taskCompletion(Request $request): JsonResponse
    {
        try {
            $filters = TaskManagementReportDTO::fromRequest($request);
            $result = $this->reportService->executeTaskCompletionReport($filters);

            return ApiResponse([
                'data' => $result['data'],
                'summary' => $result['summary'],
                'records_count' => $result['records_count'],
            ], 'Task completion report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get task productivity report
     */
    public function taskProductivity(Request $request): JsonResponse
    {
        try {
            $filters = TaskManagementReportDTO::fromRequest($request);
            $result = $this->reportService->executeTaskCompletionReport($filters);

            // Calculate productivity metrics
            $productivityData = $result['data']->groupBy('assigned_to_id')->map(function ($userData, $userId) {
                $totalTasks = $userData->count();
                $completedTasks = $userData->where('status', 'completed')->count();
                $overdueTasks = $userData->filter(function ($task) {
                    return $task->due_date && \Carbon\Carbon::parse($task->due_date)->isPast() && $task->status !== 'completed';
                })->count();

                return [
                    'user_id' => $userId,
                    'user_name' => ($userData->first()->user_first_name ?? '') . ' ' . ($userData->first()->user_last_name ?? ''),
                    'total_tasks' => $totalTasks,
                    'completed_tasks' => $completedTasks,
                    'overdue_tasks' => $overdueTasks,
                    'completion_rate' => $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0,
                    'overdue_rate' => $totalTasks > 0 ? ($overdueTasks / $totalTasks) * 100 : 0,
                ];
            })->values();

            return ApiResponse([
                'productivity_data' => $productivityData,
                'summary' => $result['summary'],
            ], 'Task productivity report generated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get task status distribution
     */
    public function taskStatusDistribution(Request $request): JsonResponse
    {
        try {
            $filters = TaskManagementReportDTO::fromRequest($request);
            $result = $this->reportService->executeTaskCompletionReport($filters);

            $statusData = $result['data']->groupBy('status')->map(function ($statusData, $status) use ($result) {
                return [
                    'status' => $status,
                    'count' => $statusData->count(),
                    'percentage' => $result['data']->count() > 0 ? ($statusData->count() / $result['data']->count()) * 100 : 0,
                ];
            })->values();

            return ApiResponse([
                'status_data' => $statusData,
                'summary' => $result['summary'],
            ], 'Task status distribution retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get overdue task analysis
     */
    public function overdueTaskAnalysis(Request $request): JsonResponse
    {
        try {
            $filters = TaskManagementReportDTO::fromRequest($request);
            $result = $this->reportService->executeTaskCompletionReport($filters);

            $overdueData = $result['data']->filter(function ($task) {
                return $task->due_date && \Carbon\Carbon::parse($task->due_date)->isPast() && $task->status !== 'completed';
            })->groupBy('assigned_to_id')->map(function ($userData, $userId) {
                return [
                    'user_id' => $userId,
                    'user_name' => ($userData->first()->user_first_name ?? '') . ' ' . ($userData->first()->user_last_name ?? ''),
                    'overdue_count' => $userData->count(),
                    'oldest_overdue' => $userData->min('due_date'),
                    'tasks' => $userData->map(function ($task) {
                        $daysOverdue = $task->due_date ? \Carbon\Carbon::parse($task->due_date)->diffInDays(now()) : 0;
                        return [
                            'id' => $task->id,
                            'title' => $task->title,
                            'due_date' => $task->due_date,
                            'days_overdue' => $daysOverdue,
                        ];
                    })->values(),
                ];
            })->values();

            return ApiResponse([
                'overdue_data' => $overdueData,
                'summary' => $result['summary'],
            ], 'Overdue task analysis retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
