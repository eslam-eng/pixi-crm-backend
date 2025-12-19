<?php

namespace App\Http\Controllers\Central\Api;

use App\DTO\Central\DiscountCodeDTO;
use App\Exceptions\DiscountCodeException;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\DiscountCodeIndexRequest;
use App\Http\Requests\Central\DiscountCodeRequest;
use App\Http\Resources\Central\DiscountResource;
use App\Models\Central\DiscountCode;
use App\Services\Central\Discount\DiscountCodeService;
use App\Services\Central\ActivationCode\ActivationCodeService;

class DiscountCodeController extends Controller
{
    public function __construct(
        private readonly DiscountCodeService $discountCodeService,
        private readonly ActivationCodeService $activationCodeService
    ) {
    }

    public function index(DiscountCodeIndexRequest $request)
    {
        $filters = $request->all();
        $limit = $request->input('limit', 15);
        $discounts = $this->discountCodeService->paginate(filters: $filters, perPage: $limit);

        return ApiResponse::success(
            data: DiscountResource::collection($discounts)->response()->getData(true),
            message: 'Discount retrieved successfully',
            code: 200,
        );
    }

    public function store(DiscountCodeRequest $request)
    {
        $discountDTO = DiscountCodeDTO::fromRequest($request);
        $this->discountCodeService->create($discountDTO);

        return ApiResponse::success(
            message: 'Discount created successfully',
            code: 201
        );
    }

    public function show(int $discount)
    {
        $discount = $this->discountCodeService->findById(id: $discount, withRelation: ['plan:id,name']);

        return ApiResponse::success(
            data: DiscountResource::make($discount),
            message: 'Discount retrieved successfully',
            code: 200
        );
    }

    public function update(DiscountCodeRequest $request, int $discount)
    {
        $discountDTO = DiscountCodeDTO::fromRequest($request);
        $this->discountCodeService->update($discount, $discountDTO);

        return ApiResponse::success(
            message: 'Discount updated successfully'
        );
    }

    public function destroy(DiscountCode $discount_code)
    {
        $this->discountCodeService->delete($discount_code);

        return ApiResponse::success(message: 'Discount deleted successfully');
    }

    public function toggleStatus(DiscountCode $discount)
    {
        $this->discountCodeService->toggleStatus($discount);
        $status = $discount->refresh()->status->getLabel();

        return ApiResponse::success(
            message: "Discount {$status} successfully",
            data: ['status' => $status]
        );
    }

    public function validateDiscountCode($discount_code, $plan_id)
    {
        try {
            $this->discountCodeService->validateDiscountForPlan(code: $discount_code, planId: $plan_id, tenant: auth()->user()->tenant);

            return ApiResponse::success(message: 'Discount code is valid');
        } catch (DiscountCodeException $exception) {
            return ApiResponse::badRequest(message: $exception->getMessage());
        } catch (\Exception $exception) {
            return ApiResponse::error(message: 'Something went wrong please try again later');
        }
    }
    public function generateCode()
    {
        $code = $this->activationCodeService->generateCode();

        return ApiResponse::success(data: $code);
    }
}
