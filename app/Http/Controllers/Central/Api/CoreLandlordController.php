<?php

namespace App\Http\Controllers\Central\Api;

use App\Enums\Landlord\FeatureGroupEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\Central\PlanResource;
use App\Services\Central\Plan\PlanService;
use Illuminate\Http\Request;

class CoreLandlordController extends Controller
{
    public function __construct(private readonly  PlanService $planService)
    {
    }

    public function plans(Request $request)
    {
        $filters = $request->all();
        $filters['is_active'] = true;
    
        $plans = $this->planService->activePlans(filters: $filters);

        return PlanResource::collection($plans);
    }
}
