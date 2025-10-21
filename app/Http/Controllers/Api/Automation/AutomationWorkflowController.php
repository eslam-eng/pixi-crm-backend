<?php

namespace App\Http\Controllers\Api\Automation;

use App\DTO\Automation\AutomationWorkflowDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\AutomationWorkflowRequest;
use App\Http\Resources\Tenant\Automation\{
    AutomationWorkflowResource,
    AutomationWorkflowShowResource
};
use App\Services\Tenant\Automation\AutomationWorkflowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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

        return response()->json([
            'success' => true,
            'data' => AutomationWorkflowResource::collection($workflows),
        ]);
    }

    /**
     * Get automation workflow by ID
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $workflow = $this->automationWorkflowService->getById($id);

        if (!$workflow) {
            return response()->json([
                'success' => false,
                'message' => 'Automation workflow not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new AutomationWorkflowShowResource($workflow),
        ]);
    }

    /**
     * Create new automation workflow
     */
    public function store(AutomationWorkflowRequest $request): JsonResponse
    {
        try {
            $dto = AutomationWorkflowDTO::fromArray($request->validated());
            $workflow = $this->automationWorkflowService->create($dto);

            return response()->json([
                'success' => true,
                'message' => 'Automation workflow created successfully',
                'data' => new AutomationWorkflowShowResource($workflow),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create automation workflow',
                'error' => $e->getMessage()
            ], 500);
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
                return response()->json([
                    'success' => false,
                    'message' => 'Automation workflow not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Automation workflow updated successfully',
                'data' => new AutomationWorkflowShowResource($workflow),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update automation workflow',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete automation workflow
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $deleted = $this->automationWorkflowService->delete($id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Automation workflow not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Automation workflow deleted successfully',
        ]);
    }

    /**
     * Toggle workflow active status
     */
    public function toggleActive(Request $request, int $id): JsonResponse
    {
        $updated = $this->automationWorkflowService->toggleActive($id);

        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'Automation workflow not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Automation workflow status updated successfully',
        ]);
    }
}
