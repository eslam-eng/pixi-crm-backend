<?php

namespace App\Http\Controllers\Central\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Central\PlanResource;
use App\Http\Resources\Central\DepartmentResource;
use App\Http\Resources\Central\SourceResource;
use App\Http\Resources\Central\IndustryResource;
use App\Http\Resources\Central\FeatureDDLResource;
use App\Services\Central\ActivationCode\ActivationCodeService;
use App\Services\Central\DepartmentService;
use App\Services\Central\Discount\DiscountCodeService;
use App\Services\Central\FeatureService;
use App\Services\Central\PlanService;
use App\Services\Central\SourceService;
use App\Services\Central\IndustryService;
use Illuminate\Http\Request;

class CoreLandlordController extends Controller
{
    public function __construct(
        private readonly PlanService $planService,
        private readonly FeatureService $featureService,
        private readonly DepartmentService $departmentService,
        private readonly SourceService $sourceService,
        private readonly IndustryService $industryService,
        private readonly ActivationCodeService $activationCodeService,
        private readonly DiscountCodeService $discountCodeService
    ) {
    }

    public function plans(Request $request)
    {
        $filters = $request->all();
        $filters['is_active'] = true;

        $plans = $this->planService->activePlans(filters: $filters);

        $data = PlanResource::collection($plans);

        return apiResponse(
            message: 'success',
            data: $data
        );
    }

    public function departments(Request $request)
    {
        $filters = $request->all();

        // Get all departments without pagination for core API
        $departments = $this->departmentService->index();

        $data = DepartmentResource::collection($departments);

        return apiResponse(
            message: 'Departments retrieved successfully',
            data: $data
        );
    }

    public function sources(Request $request)
    {
        $filters = $request->all();

        // Get all sources
        $sources = $this->sourceService->list(filters: $filters);

        $data = SourceResource::collection($sources);

        return apiResponse(
            message: 'Sources retrieved successfully',
            data: $data
        );
    }

    public function industries(Request $request)
    {
        $filters = $request->all();

        // Get all industries
        $industries = $this->industryService->list(filters: $filters);

        $data = IndustryResource::collection($industries);

        return apiResponse(
            message: 'Industries retrieved successfully',
            data: $data
        );
    }

    public function getFeatures(Request $request)
    {
        $filters = $request->all();

        $features = $this->featureService->getFeatures(filters: $filters);

        $data = FeatureDDLResource::collection($features);

        return apiResponse(
            message: 'Features retrieved successfully',
            data: $data
        );
    }

    public function checkActivationCode(Request $request)
    {
        $code = $request->input('code');

        $activationCode = $this->activationCodeService->checkActivationCode($code);

        if (!$activationCode) {
            return apiResponse(
                message: 'Activation code not found',
                data: null,
                code: 400
            );
        }

        return apiResponse(
            message: 'Activation code found',
            data: $activationCode
        );
    }

    public function checkDiscountCode(Request $request)
    {
        $code = $request->input('code');

        $discountCode = $this->discountCodeService->checkDiscountCode($code);

        if (!$discountCode) {
            return apiResponse(
                message: 'Discount code not found',
                data: null,
                code: 400
            );
        }

        return apiResponse(
            message: 'Discount code found',
            data: $discountCode
        );
    }
}
