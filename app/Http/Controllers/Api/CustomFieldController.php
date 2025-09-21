<?php

namespace App\Http\Controllers\Api;

use App\DTO\CustomField\CustomFieldDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomFields\StoreCustomFieldRequest;
use App\Services\CustomFieldService;
use DB;
use Exception;

class CustomFieldController extends Controller
{
    public function __construct(public CustomFieldService $customFieldService) {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomFieldRequest $request)
    {
        try {
            DB::beginTransaction();
            $customFieldDTO = CustomFieldDTO::fromRequest($request);
            $customField = $this->customFieldService->store($customFieldDTO);
            DB::commit();
            return ApiResponse(message: 'Custom field created successfully', code: 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
