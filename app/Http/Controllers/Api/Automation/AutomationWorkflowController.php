<?php

namespace App\Http\Controllers\Api\Automation;

use App\DTO\Automation\AutomationWorkflowDTO;
use App\Enums\AutomationAssignStrategiesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\AutomationWorkflowRequest;
use App\Http\Resources\Tenant\Automation\{
    AutomationWorkflowResource,
    AutomationWorkflowShowResource
};
use App\Services\Tenant\Automation\AutomationWorkflowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AutomationWorkflowController extends Controller
{
    protected AutomationWorkflowService $automationWorkflowService;

    public function __construct(AutomationWorkflowService $automationWorkflowService)
    {
        $this->automationWorkflowService = $automationWorkflowService;
    }

    /**
     * Get all automation workflows
     */
    public function index(Request $request): JsonResponse
    {
        $workflows = $this->automationWorkflowService->getAll();
        $data = AutomationWorkflowResource::collection($workflows)->response()->getData(true);
        return apiResponse(
            data: $data,
            message: 'Workflows retrieved successfully'
        );
    }

    /**
     * Get automation workflow by ID
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $workflow = $this->automationWorkflowService->getById($id);

        if (!$workflow) {
            return apiResponse(
                message: 'Automation workflow not found',
                code: 404
            );
        }

        return apiResponse(
            data: new AutomationWorkflowShowResource($workflow),
            message: 'Workflow retrieved successfully'
        );
    }

    /**
     * Create new automation workflow
     */
    public function store(AutomationWorkflowRequest $request): JsonResponse
    {
        try {
            $dto = AutomationWorkflowDTO::fromArray($request->validated());
            $workflow = $this->automationWorkflowService->create($dto);
            return apiResponse(
                data: new AutomationWorkflowShowResource($workflow),
                message: 'Automation workflow created successfully',
                code: 201
            );
        } catch (\Exception $e) {
            return apiResponse(
                data: ['error' => $e->getMessage()],
                message: 'Failed to create automation workflow',
                code: 500
            );
        }
    }

    /**
     * Update automation workflow
     */
    public function update(AutomationWorkflowRequest $request, int $id): JsonResponse
    {
        try {
            $dto = AutomationWorkflowDTO::fromArray($request->validated());
            $workflow = $this->automationWorkflowService->update($id, $dto);

            if (!$workflow) {
                return apiResponse(
                    message: 'Automation workflow not found',
                    code: 404
                );
            }

            return apiResponse(
                data: new AutomationWorkflowShowResource($workflow),
                message: 'Automation workflow updated successfully'
            );

        } catch (\Exception $e) {
            return apiResponse(
                data: ['error' => $e->getMessage()],
                message: 'Failed to update automation workflow',
                code: 500
            );
        }
    }

    /**
     * Delete automation workflow
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $deleted = $this->automationWorkflowService->delete($id);

        if (!$deleted) {
            return apiResponse(
                message: 'Automation workflow not found',
                code: 404
            );
        }

        return apiResponse(
            message: 'Automation workflow deleted successfully'
        );
    }

    /**
     * Toggle workflow active status
     */
    public function toggleActive(Request $request, int $id): JsonResponse
    {
        $updated = $this->automationWorkflowService->toggleActive($id);

        if (!$updated) {
            return apiResponse(
                message: 'Automation workflow not found',
                code: 404
            );
        }

        return apiResponse(
            message: 'Automation workflow status updated successfully'
        );
    }

    public function getAssignedStrategies()
    {
        $data = AutomationAssignStrategiesEnum::values();
        return apiResponse(data: $data, message: 'Data retrieved successfully', code: 200);

    }
}
