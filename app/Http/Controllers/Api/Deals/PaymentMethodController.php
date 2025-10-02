<?php

namespace App\Http\Controllers\Api\Deals;

use App\Exceptions\GeneralException;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\Tenant\Deals\PaymentMethodResource;
use App\Models\Tenant\PaymentMethod;
use App\Services\Tenant\Deals\PaymentMethodService;
use Illuminate\Http\Request;


class PaymentMethodController extends Controller
{
    public function __construct(public PaymentMethodService $paymentMethodService)
    {
        $this->middleware('permission:view-deals')->only(['index']);
        $this->middleware('permission:manage-settings')->except(['store', 'setDefault', 'setChecked', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $paymentMethods = PaymentMethod::query();
            if ($request->has('ddl')) {
                $paymentMethods = $paymentMethods->where('is_checked', true)->orderBy('name')->get();
                $paymentMethods = PaymentMethodResource::collection($paymentMethods);
            } else {
                $paymentMethods = $paymentMethods->orderBy('id', 'asc')
                    ->orderBy('name')
                    ->get();
                $paymentMethods = PaymentMethodResource::collection($paymentMethods);
            }


            return ApiResponse($paymentMethods, 'Payment methods retrieved successfully', 200);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            DB::beginTransaction();
            $data = $request->validate([
                'name' => 'required|string|max:255',
            ]);
            $data['is_manual_added'] = true;
            PaymentMethod::create($data);
            DB::commit();
            return ApiResponse(message: 'payment method created successfully', code: 201);
        } catch (GeneralException $e) {
            return ApiResponse(message: $e->getMessage(), code: $e->getCode());
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Set a priority as default.
     */
    public function setDefault(int $id)
    {
        try {
            DB::beginTransaction();
            $priority = $this->paymentMethodService->setDefault($id);
            DB::commit();
            return apiResponse(new paymentMethodService($priority), 'Priority set as default successfully');
        } catch (GeneralException $e) {
            DB::rollBack();
            return apiResponse(message: $e->getMessage(), code: 400);
        } catch (Exception $e) {
            DB::rollBack();
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Set a priority as default.
     */
    public function setChecked(int $id)
    {
        try {
            DB::beginTransaction();
            $priority = $this->paymentMethodService->setChecked($id);
            DB::commit();
            return apiResponse(new paymentMethodService($priority), 'Checked updated successfully');
        } catch (GeneralException $e) {
            DB::rollBack();
            return apiResponse(message: $e->getMessage(), code: 400);
        } catch (Exception $e) {
            DB::rollBack();
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            $this->paymentMethodService->destroy($id);
            return ApiResponse(message: 'Payment method deleted successfully', code: 200);
        } catch (GeneralException $e) {
            return ApiResponse(message: $e->getMessage(), code: $e->getCode());
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
