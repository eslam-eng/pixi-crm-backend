<?php

namespace App\Services\Central\Invoice;

use App\DTO\Central\InvoiceDTO;
use App\Enums\Landlord\InvoiceStatusEnum;
use App\Enums\Landlord\PaymentMethodEnum;
use App\Enums\Landlord\SubscriptionBillingCycleEnum;
use App\Enums\Landlord\SubscriptionStatusEnum;
use App\Exceptions\TrialException;
use App\Mail\InvoicePaymentSucceededMail;
use App\Models\Central\ActivationCode;
use App\Models\Central\DiscountCode;
use App\Models\Central\DiscountCodeUsage;
use App\Models\Central\Filters\InvoiceFilters;
use App\Models\Central\Invoice;
use App\Models\Central\InvoiceItem;
use App\Models\Central\Plan;
use App\Models\Central\Subscription;
use App\Models\Central\User;
use App\Services\Central\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class InvoiceService extends BaseService
{
    protected function getFilterClass(): ?string
    {
        return InvoiceFilters::class;
    }

    protected function baseQuery(): Builder
    {
        return Invoice::query();
    }

    public function paginate(?array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->getQuery(filters: $filters)
            ->with(['tenant:id,name'])
            ->withExists('discountCodeUsage as use_discount_code')
            ->paginate($perPage);
    }

    /**
     * @throws TrialException
     * @throws \Throwable
     */
    public function create(InvoiceDTO $invoiceDTO)
    {
        return DB::connection('landlord')
            ->transaction(function () use ($invoiceDTO) {

                $invoice = $this->getQuery()->create($invoiceDTO->toArray());

                $invoiceItems = collect($invoiceDTO->invoiceItems)->map(function ($item) use ($invoice) {
                    return array_merge($item, [
                        'invoice_id' => $invoice->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                })->toArray();

                InvoiceItem::query()->insert($invoiceItems);

                // Record discount code usage
                $this->createDiscountCodeUsage($invoiceDTO, $invoice);

                return $invoice;
            });
    }

    private function createDiscountCodeUsage(InvoiceDTO $invoiceDTO, Invoice $invoice): void
    {
        if ($invoiceDTO->discountCode) {
            $discount_code_id = $invoiceDTO->discountCode instanceof DiscountCode ? $invoiceDTO->discountCode->id : $invoiceDTO->discountCode;
            $discountCodeData = [
                'discount_code_id' => $discount_code_id,
                'tenant_id' => $invoiceDTO->tenant_id,
                'subscription_id' => $invoiceDTO->subscription_id,
                'invoice_id' => $invoice->id,
            ];
            DiscountCodeUsage::query()->create($discountCodeData);
        }
    }

    public function markAsPaid(string|Invoice $invoice): void
    {
        if (is_string($invoice)) {
            $invoice = $this->findById($invoice);
        }

        DB::connection('landlord')->transaction(function () use ($invoice) {
            // cancel any active subscriptions
            Subscription::query()
                ->where('tenant_id', $invoice->tenant_id)
                ->where('status', SubscriptionStatusEnum::ACTIVE->value)
                ->where('id', '!=', $invoice->subscription_id)
                ->update([
                    'status' => SubscriptionStatusEnum::CANCELED->value,
                ]);

            logger('invoice paid : ' . $invoice->id);
            logger('subscription is : ' . $invoice->subscription_id);
            // Ensure the current subscription stays active
            Subscription::query()
                ->where('id', $invoice->subscription_id)
                ->update([
                    'status' => SubscriptionStatusEnum::ACTIVE->value,
                ]);
            // update invoice status to paid
            $invoice->markAsPaid();
        });

        Mail::to($invoice->tenant->owner->email)->queue(new InvoicePaymentSucceededMail(invoice: $invoice));
    }

    public function prepareForPaidSubscription(
        Plan $plan,
        SubscriptionBillingCycleEnum $duration,
        User $user,
        ?DiscountCode $discountCode = null,
        ?string $notes = null,
    ): InvoiceDTO {
        $price = calculateSubscriptionAmount(plan: $plan, duration: $duration);
        $discountPercentage = $discountCode?->discount_percentage ?? 0;
        $total = $price - ($price * $discountPercentage) / 100;

        $invoiceItems = [
            [
                'description' => "Plan " . (is_array($plan->name) ? ($plan->name[app()->getLocale()] ?? $plan->name['en'] ?? '') : $plan->name),
                'unit_price' => $price,
                'total' => $price,
            ],
        ];

        if ($discountCode) {
            $invoiceItems[] = [
                'description' => "Discount ({$discountCode->discount_code}): {$discountPercentage}% off",
                'unit_price' => -($price * $discountPercentage) / 100,
                'total' => -($price * $discountPercentage) / 100,
            ];
        }

        return new InvoiceDTO(
            tenant_id: $user->tenant_id,
            user_id: $user->id,
            subtotal: $price,
            discount_percentage: $discountPercentage,
            total: $total,
            status: InvoiceStatusEnum::PENDING->value,
            due_date: now(),
            notes: $notes,
            payment_method: PaymentMethodEnum::CARD->value,
            invoiceItems: $invoiceItems,
        );
    }

    public function prepareForActivationCode(ActivationCode $activationCode, User $user): InvoiceDTO
    {
        $invoiceItems = [
            [
                'description' => "Plan " . (is_array($activationCode->plan->name) ? ($activationCode->plan->name[app()->getLocale()] ?? $activationCode->plan->name['en'] ?? '') : $activationCode->plan->name),
                'unit_price' => $activationCode->plan->lifetime_price,
                'total' => $activationCode->plan->lifetime_price,
            ],
            [
                'description' => "Activation Code ({$activationCode->code}): 100% off",
                'unit_price' => -$activationCode->plan->lifetime_price,
                'total' => -$activationCode->plan->lifetime_price,
            ],
        ];

        return new InvoiceDTO(
            tenant_id: $user->tenant_id,
            user_id: $user->id,
            subtotal: $activationCode->plan->lifetime_price,
            discount_percentage: 100,
            total: 0,
            status: InvoiceStatusEnum::PAID->value,
            payment_method: PaymentMethodEnum::ACTIVATION_CODE->value,
            paid_at: now(),
            invoiceItems: $invoiceItems
        );
    }

    public function createFromSubscription(Subscription $subscription, ?string $notes = null, ?int $status = null, ?string $paymentMethod = null): Invoice
    {
        $invoiceItems = [
            [
                'description' => "Subscription for plan: {$subscription->plan_name}",
                'quantity' => 1,
                'unit_price' => $subscription->amount,
                'total' => $subscription->amount,
            ],
        ];

        $invoiceDTO = new InvoiceDTO(
            tenant_id: $subscription->tenant_id,
            subscription_id: $subscription->id,
            subtotal: $subscription->amount,
            total: $subscription->amount,
            status: $status ?? InvoiceStatusEnum::PENDING->value,
            due_date: now()->addDays(7), // Default due date
            notes: $notes ?? "Subscription renewal/creation for {$subscription->plan_name}",
            payment_method: $paymentMethod,
            paid_at: ($status === InvoiceStatusEnum::PAID->value) ? now() : null,
            invoiceItems: $invoiceItems,
        );

        return $this->create($invoiceDTO);
    }
    public function statics()
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();

        // Total Transactions
        $totalTransactions = Invoice::count();
        $lastMonthTotalTransactions = Invoice::where('created_at', '<', $startOfMonth)->count();
        $totalTransactionsGrowth = $lastMonthTotalTransactions > 0
            ? (($totalTransactions - $lastMonthTotalTransactions) / $lastMonthTotalTransactions) * 100
            : ($totalTransactions > 0 ? 100 : 0);

        // Successful Payments
        $successfulPayments = Invoice::where('status', InvoiceStatusEnum::PAID->value)->count();
        $successRate = $totalTransactions > 0 ? ($successfulPayments / $totalTransactions) * 100 : 0;

        // Total Revenue
        $totalRevenue = Invoice::where('status', InvoiceStatusEnum::PAID->value)->sum('total');
        $lastMonthTotalRevenue = Invoice::where('status', InvoiceStatusEnum::PAID->value)
            ->where('created_at', '<', $startOfMonth)
            ->sum('total');
        $revenueGrowth = $lastMonthTotalRevenue > 0
            ? (($totalRevenue - $lastMonthTotalRevenue) / $lastMonthTotalRevenue) * 100
            : ($totalRevenue > 0 ? 100 : 0);

        // Pending/Failed Payments
        $pendingPayments = Invoice::where('status', InvoiceStatusEnum::PENDING->value)->count();
        $failedPayments = Invoice::where('status', InvoiceStatusEnum::FAILED->value)->count();

        return [
            'total_transactions' => [
                'value' => $totalTransactions,
                'growth' => round($totalTransactionsGrowth, 2),
            ],
            'successful_payments' => [
                'value' => $successfulPayments,
                'rate' => round($successRate, 2),
            ],
            'total_revenue' => [
                'value' => round((float) $totalRevenue, 2),
                'growth' => round($revenueGrowth, 2),
            ],
            'pending_payments' => [
                'value' => $pendingPayments,
                'failed_count' => $failedPayments,
            ]
        ];
    }
}
