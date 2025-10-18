<?php

namespace App\Http\Controllers\Api\Report;

use App\DTO\Report\ReportDTO;
use App\DTO\Report\ReportFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Report\ReportRequest;
use App\Http\Resources\Report\ReportResource;
use App\Http\Resources\Report\ReportExecutionResource;
use App\Services\Report\ReportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {
        // $this->middleware('permission:view-reports')->only(['index', 'show']);
        // $this->middleware('permission:create-reports')->only(['store']);
        // $this->middleware('permission:edit-reports')->only(['update']);
        // $this->middleware('permission:delete-reports')->only(['destroy']);
        // $this->middleware('permission:execute-reports')->only(['execute', 'export']);
    }

    /**
     * Get all reports
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = array_filter($request->all(), function ($value) {
                return $value !== null && $value !== '';
            });

            $withRelations = ['createdBy', 'latestExecution'];

            if ($request->has('ddl')) {
                $reports = $this->reportService->index($filters, $withRelations, 'all');
                $data = ReportResource::collection($reports);
            } else {
                $reports = $this->reportService->index($filters, $withRelations, $filters['per_page'] ?? 15);
                $data = ReportResource::collection($reports)->response()->getData(true);
            }

            return ApiResponse($data, 'Reports retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Create a new report
     */
    public function store(ReportRequest $request): JsonResponse
    {
        try {
            $reportDTO = ReportDTO::fromRequest($request);
            $reportDTO->created_by_id = Auth::id();

            $report = $this->reportService->store($reportDTO);

            return ApiResponse(
                new ReportResource($report),
                'Report created successfully',
                code: Response::HTTP_CREATED
            );
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get a specific report
     */
    public function show(int $id): JsonResponse
    {
        try {
            $report = $this->reportService->findById($id, withRelations: ['createdBy', 'executions']);

            return ApiResponse(
                new ReportResource($report),
                'Report retrieved successfully'
            );
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Update a report
     */
    public function update(ReportRequest $request, int $id): JsonResponse
    {
        try {
            $reportDTO = ReportDTO::fromRequest($request);
            $report = $this->reportService->update($reportDTO, $id);

            return ApiResponse(
                new ReportResource($report),
                'Report updated successfully'
            );
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Delete a report
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->reportService->destroy($id);

            return ApiResponse(
                null,
                'Report deleted successfully'
            );
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Execute a report
     */
    public function execute(Request $request, int $id): JsonResponse
    {
        try {
            $filters = ReportFilterDTO::fromRequest($request);
            $execution = $this->reportService->executeReport($id, $filters);

            return ApiResponse(
                new ReportExecutionResource($execution),
                'Report executed successfully'
            );
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Export a report
     */
    public function export(Request $request, int $id): JsonResponse
    {
        try {
            $format = $request->input('format', 'excel'); // excel, pdf, csv
            $filters = ReportFilterDTO::fromRequest($request);

            $execution = $this->reportService->executeReport($id, $filters);

            // Generate export file based on format
            $filePath = $this->generateExportFile($execution, $format);

            return ApiResponse([
                'execution' => new ReportExecutionResource($execution),
                'download_url' => $filePath,
            ], 'Report exported successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get report categories
     */
    public function categories(): JsonResponse
    {
        $categories = [
            'sales_performance' => 'Sales Performance',
            'lead_management' => 'Lead Management',
            'team_performance' => 'Team Performance',
            'task_completion' => 'Task Completion',
            'revenue_analysis' => 'Revenue Analysis',
            'opportunity_pipeline' => 'Opportunity Pipeline',
            'call_activity' => 'Call Activity',
            'contact_management' => 'Contact Management',
            'product_performance' => 'Product Performance',
            'forecasting' => 'Forecasting',
        ];

        return ApiResponse($categories, 'Report categories retrieved successfully');
    }

    /**
     * Get report types by category
     */
    public function types(Request $request): JsonResponse
    {
        $category = $request->input('category');

        $types = [
            'sales_performance' => [
                'deals_performance' => 'Deals Performance Report',
                'revenue_analysis' => 'Revenue Analysis Report',
            ],
            'lead_management' => [
                'lead_generation' => 'Lead Generation Report',
                'lead_conversion' => 'Lead Conversion Report',
            ],
            'team_performance' => [
                'individual_performance' => 'Individual Performance Report',
                'team_performance' => 'Team Performance Report',
                'target_vs_achievement' => 'Target vs Achievement Report',
            ],
            'task_completion' => [
                'task_completion' => 'Task Completion Report',
                'task_productivity' => 'Task Productivity Report',
            ],
            'revenue_analysis' => [
                'revenue_trends' => 'Revenue Trends Report',
                'revenue_by_product' => 'Revenue by Product Report',
            ],
            'opportunity_pipeline' => [
                'pipeline_report' => 'Pipeline Report',
                'activity_report' => 'Activity Report',
            ],
            'call_activity' => [
                'call_log_analysis' => 'Call Log Analysis Report',
                'call_recording_analysis' => 'Call Recording Analysis Report',
            ],
            'contact_management' => [
                'contact_management' => 'Contact Management Report',
            ],
            'product_performance' => [
                'product_performance' => 'Product Performance Report',
            ],
            'forecasting' => [
                'sales_forecast' => 'Sales Forecast Report',
            ],
        ];

        $result = $category ? ($types[$category] ?? []) : $types;

        return ApiResponse($result, 'Report types retrieved successfully');
    }

    /**
     * Generate export file
     */
    private function generateExportFile($execution, string $format): string
    {
        // This would integrate with your export service
        // For now, returning a placeholder
        return "/exports/report_{$execution->id}.{$format}";
    }
}
