<?php

namespace App\Http\Controllers\Api;

use App\DTO\Reason\ReasonDTO;
use App\Models\Tenant\Reason;
use Exception;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Services\ReasonService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReasonResource;
use App\Http\Requests\Reason\ReasonStoreRequest;

class ReasonController extends Controller
{
    public function __construct(public ReasonService $reasonService)
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
            $resources = $this->reasonService->listing($filters, $withRelations);
            return ApiResponse::sendResponse(200, 'Reasons retrieved successfully', ReasonResource::collection($resources));
        } catch (Exception $e) {
            return ApiResponse::sendResponse(500, $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReasonStoreRequest $request)
    {
        try{
            DB::beginTransaction();
            $reasonDTO=ReasonDTO::fromRequest($request);
            $reason=$this->reasonService->store($reasonDTO);
            DB::commit();
            return ApiResponse::sendResponse(201, 'Source created successfully', new ReasonResource($reason));

        }catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::sendResponse(500, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Reason $reason)
    {
        dd($reason);
        return ApiResponse::sendResponse(200, 'Reeason retrieved successfully', new ReasonResource($reason));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
