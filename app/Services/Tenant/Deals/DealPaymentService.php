<?php

namespace App\Services\Tenant\Deals;

use App\DTO\Tenant\DealPaymentDTO;
use App\Models\Tenant\Deal;
use App\Models\Tenant\DealPayment;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class DealPaymentService  extends BaseService
{
    public function __construct(
        public DealPayment $model,
    ) {}

    public function getModel(): DealPayment
    {
        return $this->model;
    }

    /**
     * Add a new payment to a deal
     *
     * @param int $dealId
     * @param DealPaymentDTO $dealPaymentDTO
     * @return DealPayment
     */
    public function addPaymentToDeal(int $dealId, DealPaymentDTO $dealPaymentDTO): DealPayment
    {
        return DB::transaction(function () use ($dealId, $dealPaymentDTO) {
            // Create the payment
            $payment = $this->model->create([
                'deal_id' => $dealId,
                'amount' => $dealPaymentDTO->amount,
                'pay_date' => $dealPaymentDTO->pay_date,
                'payment_method_id' => $dealPaymentDTO->payment_method_id,
            ]);

            // Update deal payment status and amounts
            $this->updateDealPaymentStatus($dealId);

            return $payment;
        });
    }

    /**
     * Update deal payment status and amounts based on payments
     *
     * @param int $dealId
     * @return void
     */
    private function updateDealPaymentStatus(int $dealId): void
    {
        $deal = Deal::findOrFail($dealId);
        
        // Calculate total payments
        $totalPayments = $deal->payments()->sum('amount');
        
        // Update deal amounts
        $deal->update([
            'partial_amount_paid' => $totalPayments,
            'amount_due' => $deal->total_amount - $totalPayments,
            'payment_status' => $this->determinePaymentStatus($deal->total_amount, $totalPayments),
        ]);
    }

    /**
     * Determine payment status based on total amount and payments
     *
     * @param float $totalAmount
     * @param float $totalPayments
     * @return string
     */
    private function determinePaymentStatus(float $totalAmount, float $totalPayments): string
    {
        if ($totalPayments >= $totalAmount) {
            return 'paid';
        } elseif ($totalPayments > 0) {
            return 'partial';
        } else {
            return 'unpaid';
        }
    }

    /**
     * Get all payments for a deal
     *
     * @param int $dealId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDealPayments(int $dealId)
    {
        return $this->model->where('deal_id', $dealId)
            ->with(['payment_method'])
            ->orderBy('pay_date', 'desc')
            ->get();
    }

    /**
     * Delete a payment
     *
     * @param int $paymentId
     * @return bool
     */
    public function deletePayment(int $paymentId): bool
    {
        return DB::transaction(function () use ($paymentId) {
            $payment = $this->model->findOrFail($paymentId);
            $dealId = $payment->deal_id;
            
            // Delete the payment
            $deleted = $payment->delete();
            
            if ($deleted) {
                // Update deal payment status
                $this->updateDealPaymentStatus($dealId);
            }
            
            return $deleted;
        });
    }
}
