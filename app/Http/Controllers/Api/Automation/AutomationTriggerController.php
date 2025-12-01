<?php

namespace App\Http\Controllers\Api\Automation;

use App\Http\Controllers\Controller;
use App\Http\Resources\Tenant\Automation\AutomationTriggerResource;
use App\Services\Tenant\Automation\AutomationTriggerService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AutomationTriggerController extends Controller
{
    protected AutomationTriggerService $automationTriggerService;

    public function __construct(AutomationTriggerService $automationTriggerService)
    {
        $this->automationTriggerService = $automationTriggerService;
    }

    /**
     * Get automation triggers for dropdown
     */
    public function index(Request $request): JsonResponse
    {
        $triggers = $this->automationTriggerService->getDropdownOptions();

        return apiResponse(
            data: AutomationTriggerResource::collection($triggers),
            message: 'Triggers retrieved successfully'
        );
    }

    /**
     * Get available fields for a specific trigger
     */
    public function getFields(int $triggerId): JsonResponse
    {
        $fields = $this->automationTriggerService->getTriggerFields($triggerId);

        if (!$fields) {
            return apiResponse(
                message: 'Trigger not found',
                code: 404
            );
        }

        return apiResponse(
            data: $fields,
            message: 'Fields retrieved successfully'
        );
    }

    /**
     * Get options for a specific field
     */
    public function getFieldOptions(int $fieldId): JsonResponse
    {
        $options = $this->automationTriggerService->getFieldOptions($fieldId);

        if (!$options) {
            return apiResponse(
                message: 'Field not found',
                code: 404
            );
        }

        return apiResponse(
            data: $options,
            message: 'Field options retrieved successfully'
        );
    }
}
