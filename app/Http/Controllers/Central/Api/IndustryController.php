<?php

namespace App\Http\Controllers\Central\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\IndustryRequest;
use App\Http\Resources\Central\IndustryResource;
use App\Services\Central\IndustryService;
use Exception;

class IndustryController extends Controller
{
    public function __construct(private readonly IndustryService $industryService)
    {
    }

    public function index()
    {
        try {
            $industries = $this->industryService->index();
            $data = IndustryResource::collection($industries);
            return ApiResponse::success(data: $data, message: 'Industries retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse::error(message: $e->getMessage(), code: 500);
        }
    }

    public function store(IndustryRequest $request)
    {
        try {
            $industry = $this->industryService->store($request->validated());
            $data = new IndustryResource($industry);
            return ApiResponse::success(data: $data, message: 'Industry created successfully');
        } catch (Exception $e) {
            return ApiResponse::error(message: $e->getMessage(), code: 500);
        }
    }

    public function show(int $id)
    {
        try {
            $industry = $this->industryService->findById($id);
            $data = new IndustryResource($industry);
            return ApiResponse::success(data: $data, message: 'Industry retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse::error(message: $e->getMessage(), code: 404);
        }
    }

    public function update(IndustryRequest $request, int $id)
    {
        try {
            $industry = $this->industryService->update($id, $request->validated());
            $data = new IndustryResource($industry);
            return ApiResponse::success(data: $data, message: 'Industry updated successfully');
        } catch (Exception $e) {
            return ApiResponse::error(message: $e->getMessage(), code: 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->industryService->destroy($id);
            return ApiResponse::success(message: 'Industry deleted successfully');
        } catch (Exception $e) {
            return ApiResponse::error(message: $e->getMessage(), code: 500);
        }
    }
}
