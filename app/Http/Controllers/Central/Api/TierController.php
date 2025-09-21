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
use Request;

class TierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
        $this->middleware('permission:tiers.add')->only(['store']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $tiers = Tier::all();
            return ApiResponse(new TierCollection($tiers), 'Tiers retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTierRequest $request)
    {
        try {
            // $this->authorize('user.create');
            $data = $request->validated();
            $tier = Tier::create($data);
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

    /**
     * Display the specified resource.
     */
    public function show(Tier $tier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTierRequest $request, Tier $tier)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tier $tier)
    {
        //
    }
}
