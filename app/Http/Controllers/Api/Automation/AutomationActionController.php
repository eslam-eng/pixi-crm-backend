<?php

namespace App\Http\Controllers\Api\Automation;

use App\Http\Controllers\Controller;
use App\Http\Resources\Tenant\Automation\AutomationActionResource;
use App\Services\Tenant\Automation\AutomationActionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AutomationActionController extends Controller
{
    protected AutomationActionService $automationActionService;

    public function __construct(AutomationActionService $automationActionService)
    {
        $this->automationActionService = $automationActionService;
    }

    /**
     * Get automation actions for dropdown
     */
    public function index(Request $request): JsonResponse
    {
        $moduleName = $request->input('module_name');
        $except_trigger_id = $request->input('except_trigger_id');
        $actions = $this->automationActionService->getDropdownOptions($moduleName, $except_trigger_id);

        return apiResponse(
            data: AutomationActionResource::collection($actions),
            message: 'Actions retrieved successfully'
        );
    }
}
