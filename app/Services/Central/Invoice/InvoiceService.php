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
use App\Services\BaseService;
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
                'description' => "Plan {$plan->name}",
                'unit_price' => $price,
                'total' => $price,
            ],
        ];

        if ($discountCode) {
            $invoiceItems[] = [
                'description' => "Discount ({$discountCode->discount_code}): {$discountPercentage}% off",
                'unit_price' => - ($price * $discountPercentage) / 100,
                'total' => - ($price * $discountPercentage) / 100,
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
                'description' => "Plan {$activationCode->plan->name}",
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
}
