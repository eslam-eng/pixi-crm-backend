<?php

namespace App\Http\Controllers\Central\Api;

use App\Enums\CompanySizes;
use App\Http\Controllers\Controller;
use App\Http\Resources\Central\PlanResource;
use App\Http\Resources\Central\DepartmentResource;
use App\Http\Resources\Central\SourceResource;
use App\Http\Resources\Central\IndustryResource;
use App\Http\Resources\Central\FeatureDDLResource;
use App\Http\Resources\landloardLocation\CityResource;
use App\Http\Resources\landloardLocation\CountryResource;
use App\Services\Central\ActivationCode\ActivationCodeService;
use App\Services\Central\CityService;
use App\Services\Central\CountryService;
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
        private readonly DiscountCodeService $discountCodeService,
        private readonly CountryService $countryService,
        private readonly CityService $cityService
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

    public function countries(Request $request)
    {
        $data = $this->countryService->getAll();
        $data = CountryResource::collection($data);
        return apiResponse(
            message: 'success',
            data: $data
        );
    }
    public function cities(Request $request)
    {
        $filters['country_id'] = $request->country_id;
        $data = $this->cityService->getAll(filters: $filters);
        $data = CityResource::collection($data);
        return apiResponse(
            message: 'success',
            data: $data
        );
    }

    public function companySizes(Request $request)
    {
        $data = CompanySizes::values();

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
