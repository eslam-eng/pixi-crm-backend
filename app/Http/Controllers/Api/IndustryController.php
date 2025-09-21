<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Industries\IndustryUpdateRequest;
use Exception;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Tenant\Industry;
use App\DTO\Industry\IndustryDTO;
use App\Services\IndustryService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\IndustryResource;
use App\Http\Requests\Industries\IndustryStoreRequest;

class IndustryController extends Controller
{
    public function __construct(public IndustryService $industryService)
    {

    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $filters = array_filter($request->get('filters', []), function ($value) {
                return ($value !== null && $value !== false && $value !== '');
            });
            $withRelations = [];
            $industries = $this->industryService->listing($filters, $withRelations);
            return ApiResponse::sendResponse(200, 'Industries retrieved successfully', IndustryResource::collection($industries));
        } catch (Exception $e) {
            return ApiResponse::sendResponse(500, $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(IndustryStoreRequest $request)
    {
        try{
            DB::beginTransaction();
            $industryDTO = IndustryDTO::fromRequest($request);
            $industry = $this->industryService->store($industryDTO);
            DB::commit();
            return ApiResponse::sendResponse(201, 'Industry created successfully', new IndustryResource($industry));
        }catch (Exception $e) {
            DB::rollBack();
            return ApiResponse::sendResponse(500, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(IndustryUpdateRequest $request, int $id): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();
        $industryDTO = IndustryDTO::fromRequest($request);
        $industry = $this->industryService->update($industryDTO, $id);
        DB::commit();
        return ApiResponse::sendResponse(200, 'Industry updated successfully', new IndustryResource($industry));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
