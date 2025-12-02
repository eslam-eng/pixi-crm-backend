<?php

namespace App\Http\Controllers\Api\Automation;

use App\Enums\ConditionOperation;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class AutomationConditionController extends Controller
{
    /**
     * Get available condition operations
     */
    public function getOperations(): JsonResponse
    {
        $operations = collect(ConditionOperation::cases())->map(function ($operation) {
            return [
                'value' => $operation->value,
                'label' => $operation->label(),
                'description' => $operation->description(),
            ];
        });

        return apiResponse(
            data: $operations,
            message: 'Condition operations retrieved successfully'
        );
    }
}
