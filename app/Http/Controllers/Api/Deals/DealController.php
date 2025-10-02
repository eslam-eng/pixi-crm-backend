<?php

namespace App\Http\Controllers\Api\Deals;

use App\DTO\Tenant\DealDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Deals\ChangeApprovalStatusRequest;
use App\Http\Requests\Tenant\Deals\DealRequest;
use App\Http\Resources\Tenant\Deals\DealListResource;
use App\Http\Resources\Tenant\Deals\DealShowResource;
use App\Services\Tenant\Deals\DealService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DealController extends Controller
{
    public function __construct(public DealService $dealService) {
        $this->middleware('permission:view-deals')->only(['index', 'show']);
        $this->middleware('permission:create-deals')->only(['store']);
        $this->middleware('permission:edit-deals')->only(['update']);
        $this->middleware('permission:delete-deals')->only(['destroy']);
        $this->middleware('permission:change-approval-status')->only(['changeApprovalStatus']);
    }

    public function index(Request $request)
    {
        $deals = $this->dealService->paginate($request);
        return apiResponse(data: DealListResource::collection($deals)->response()->getData(true), message: 'Deals retrieved successfully', code: 200);
    }

    public function statistics()
    {
        $stats = $this->dealService->statistics();
        return apiResponse(data: $stats, message: 'Statistics retrieved successfully', code: 200);
    }

    public function show(int $id)
    {
        try {
            $deal = $this->dealService->show($id);
            return apiResponse(
                data: new DealShowResource($deal),
                message: 'Deal retrieved successfully',
                code: 200
            );
        } catch (\Exception $e) {
            return apiResponse(
                message: $e->getMessage(),
                code: 404
            );
        }
    }

    public function store(DealRequest $request)
    {
        try {
            $dealDTO = DealDTO::fromRequest($request);
            $deal = $this->dealService->store($dealDTO);
            return apiResponse(
                data: new DealListResource($deal),
                message: 'Deal created successfully',
                code: 201
            );
        } catch (\Exception $e) {
            return apiResponse(
                message: $e->getMessage(),
                code: 500
            );
        }
    }

    public function update(DealRequest $request, int $id)
    {
        try {
            $dealDTO = DealDTO::fromRequest($request);
            $deal = $this->dealService->update($dealDTO, $id);
            return apiResponse(
                data: [],
                message: 'Deal updated successfully',
                code: 200
            );
        } catch (ValidationException $e) {
            return apiResponse(
                message: $e->errors(),
                code: 422
            );
        } catch (\Exception $e) {
            return apiResponse(
                message: $e->getMessage(),
                code: 500
            );
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->dealService->destroy($id);
            return apiResponse(
                message: 'Deal deleted successfully',
                code: 200
            );
        } catch (\Exception $e) {
            return apiResponse(
                message: $e->getMessage(),
                code: 404
            );
        }
    }

    public function changeApprovalStatus(ChangeApprovalStatusRequest $request, int $id)
    {
        try {
            $deal = $this->dealService->changeApprovalStatus($id, $request->input('status'));
            return apiResponse(
                data: [],
                message: 'Deal approval status updated successfully',
                code: 200
            );
        } catch (ValidationException $e) {
            return apiResponse(
                message: $e->errors(),
                code: 422
            );
        } catch (\Exception $e) {
            return apiResponse(
                message: $e->getMessage(),
                code: 500
            );
        }
    }
}
