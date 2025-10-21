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

        return response()->json([
            'success' => true,
            'data' => AutomationTriggerResource::collection($triggers),
        ]);
    }



}
