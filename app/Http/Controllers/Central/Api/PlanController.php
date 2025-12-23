<?php

namespace App\Http\Controllers\Central\Api;

use App\DTO\Central\PlanDTO;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\PlanRequest;
use App\Http\Resources\Central\PlanResource;
use App\Services\Central\PlanService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function __construct(protected PlanService $planService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = array_filter([
            'is_active' => $request->query('is_active', true),
            'is_trial' => false,
        ], fn($v) => !is_null($v));

        $withRelations = [
            'limitFeatures',
            'addonFeatures',
        ];

        $plans = $this->planService->paginate(filters: $filters, withRelation: $withRelations);
        $data = PlanResource::collection($plans)->response()->getData(true);
        return ApiResponse::success(data: $data);
    }

    public function activePlans(Request $request)
    {
        $filters = array_filter([
            'is_active' => true,
            'is_trial' => false,
        ], fn($v) => !is_null($v));

        $withRelations = ['limitFeatures'];

        $plans = $this->planService->activePlans(filters: $filters, withRelation: $withRelations);

        $data = PlanResource::collection($plans)->response()->getData(true);
        return ApiResponse::success(data: $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PlanRequest $request)
    {
        try {
            $planDTO = PlanDTO::fromRequest($request);
            $this->planService->create(planDTO: $planDTO);
            return ApiResponse::success(message: __('app.created'));
        } catch (\Exception $e) {
            return ApiResponse::error(message: $e->getMessage());
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $withRelations = ['limitFeatures', 'addonFeatures'];
            // dd($withRelations, $id);
            $plan = $this->planService->findById(id: $id, withRelation: $withRelations);
            return ApiResponse::success(data: PlanResource::make($plan));
        } catch (NotFoundHttpException $e) {
            return ApiResponse::notFound(message: $e->getMessage());
        } catch (\Exception $e) {
            return ApiResponse::error(message: $e->getMessage());
        }
    }

    public function statics()
    {
        $statics = $this->planService->statics();
        // overwrite avg price value to be rounded
        // Safely handle the average price with null coalescing
        $statics['avg_price'] = isset($statics['avg_price'])
            ? round((float) $statics['avg_price'], 2)
            : 0.00;

        return ApiResponse::success(data: $statics);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PlanRequest $request, string $id)
    {
        $planDTO = PlanDTO::fromRequest($request);
        $this->planService->update(planDTO: $planDTO, plan: $id);

        return ApiResponse::success(message: __('app.plan_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->planService->delete($id);
            return ApiResponse::success(message: 'Plan deleted successfully');
        } catch (NotFoundHttpException $e) {
            return ApiResponse::notFound(message: $e->getMessage());
        }
    }
}
