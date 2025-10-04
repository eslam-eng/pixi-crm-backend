<?php

namespace App\Http\Controllers\Api\Deals;

use App\DTO\Tenant\DealPaymentDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Deals\DealPaymentRequest;
use App\Models\Tenant\Deal;
use App\Services\Tenant\Deals\DealPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DealPaymentController extends Controller
{
    public function __construct(
        private DealPaymentService $dealPaymentService
    ) {
        $this->middleware('permission:view-deals')->only(['index']);
        $this->middleware('permission:manage-settings')->except(['index']);
    }

    /**
     * Add a new payment to a deal
     *
     * @param DealPaymentRequest $request
     * @param int $dealId
     * @return JsonResponse
     */
    public function store(DealPaymentRequest $request, int $dealId): JsonResponse
    {
        try {
            // Verify deal exists
            $deal = Deal::findOrFail($dealId);

            // Create DTO from request
            $dealPaymentDTO = DealPaymentDTO::fromArray($request->validated());

            // Add payment to deal
            $payment = $this->dealPaymentService->addPaymentToDeal($dealId, $dealPaymentDTO);

            return response()->json([
                'success' => true,
                'message' => 'Payment added successfully',
                'data' => [
                    'payment' => $payment->load('payment_method'),
                    'deal' => $deal->fresh(['payments'])
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all payments for a deal
     *
     * @param int $dealId
     * @return JsonResponse
     */
    public function index(int $dealId): JsonResponse
    {
        try {
            // Verify deal exists
            Deal::findOrFail($dealId);

            $payments = $this->dealPaymentService->getDealPayments($dealId);

            return response()->json([
                'success' => true,
                'data' => $payments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a payment
     *
     * @param int $dealId
     * @param int $paymentId
     * @return JsonResponse
     */
    public function destroy(int $dealId, int $paymentId): JsonResponse
    {
        try {
            // Verify deal exists
            Deal::findOrFail($dealId);

            $deleted = $this->dealPaymentService->deletePayment($paymentId);

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment deleted successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete payment'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
