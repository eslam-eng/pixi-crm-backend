<?php

namespace App\Http\Controllers\Api;

use App\Enums\CustomFieldTypeEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CustomFieldTypesController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $types = CustomFieldTypeEnum::toArray();
            return ApiResponse($types, 'Custom field types retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
