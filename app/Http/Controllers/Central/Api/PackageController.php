<?php

namespace App\Http\Controllers\Central\Api;

use App\Http\Controllers\Controller;
use App\Models\Tier;
use App\Http\Requests\StoreTierRequest;
use App\Http\Requests\Tier\BuyTierRequest;
use App\Http\Requests\UpdateTierRequest;
use App\Http\Resources\TierCollection;
use App\Http\Resources\TierResource;
use App\Models\Tenant;
use Auth;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Services\Central\TierService;
use App\DTO\Tier\TierDTO;
use App\Models\Filters\TierFilter;

class PackageController extends Controller
{
    public function __construct(public TierService $tierService)
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
        // $this->middleware('permission:tiers.add')->only(['store']);
    }

    public function get_statistics()
    {
        try {
            $totalPackages = Tier::count();
            $activePackages = Tier::where('status', 'active')->count();
            $inactivePackages = Tier::where('status', 'inactive')->count();

            return ApiResponse([
                'total_packages' => $totalPackages,
                'active_packages' => $activePackages,
                'inactive_packages' => $inactivePackages,
            ], 'Package statistics retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $query = Tier::query();

            // Apply filters using TierFilter
            $filters = $request->only([
                'package_name', 
                'status',
                'duration_unit',
                'price_type',

                'availability',
                'min_users',
                'min_price',
                'max_users',
                'max_price',
                'after_date',
                'before_date',
                'module_id',
                'max_users_range'
            ]);

            // Remove empty values
            $filters = array_filter($filters, function ($value) {
                return $value !== null && $value !== '';
            });

            $tierFilter = new TierFilter($filters);
            $query = $tierFilter->apply($query);

            // Get pagination per page from request or default to 10


            // Paginate the results
            $tiers = $query->paginate(per_page());
            // dd($tiers);
            $data = TierResource::collection($tiers)->response()->getdata(true);
            return ApiResponse($data, __('app.data added successfully'));
        } catch (\Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function store(StoreTierRequest $request)
    {
        try {
            // $this->authorize('user.create');
            $tierDTO = TierDTO::fromRequest($request);
            $tier = $this->tierService->store($tierDTO);

            return ApiResponse(new TierResource($tier), 'Tier created successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function buy(BuyTierRequest $request)
    {
        try {
            // $this->authorize('buy', $tier);
            $tier = Tier::where('activation_code', $request->activation_code)->first(); // Assuming the activation code has a relationship with the tier
            if (!$tier) {
                return ApiResponse(message: 'Tier not found for the provided activation code', code: 404);
            }
            $tenant = Tenant::find($request->tenant_id);
            $tenant->tiers()->attach($tier->id, [
                'user_id' => Auth::user()->id,
                'activated_at' => now(),
            ]);

            return ApiResponse(message: 'Tier purchased successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function show(int $tier_id)
    {
        $tier = Tier::findOrFail($tier_id);

        // Check if the user has permission to view the tier
        // $this->authorize('view', $tier);

        return ApiResponse(new TierResource($tier), 'Tier retrieved successfully');
    }

    public function update(UpdateTierRequest $request, Tier $tier)
    {
        try {
            // $this->authorize('user.update');
            $data = $request->validated();
            $tier->update($data);
            return ApiResponse(new TierResource($tier), 'Tier updated successfully');
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'Tier not found', code: 404);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function destroy(Tier $tier)
    {
        try {
            // Check if tier has active subscriptions
            if ($tier->subscriptions()->exists()) {
                return ApiResponse(
                    message: 'Cannot delete tier with active subscriptions',
                    code: 422
                );
            }

            $tier->delete();
            return ApiResponse(message: 'Tier deleted successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
