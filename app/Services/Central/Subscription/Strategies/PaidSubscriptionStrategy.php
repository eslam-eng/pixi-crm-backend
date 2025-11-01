<?php

namespace App\Services\Central\Subscription\Strategies;

use App\DTOs\Landlord\SubscriptionDTO;
use App\Enum\SubscriptionBillingCycleEnum;
use App\Enum\SubscriptionStatusEnum;
use App\Enum\SubscriptionTypeEnum;
use App\Exceptions\DiscountCodeException;
use App\Models\Landlord\DiscountCodeUsage;
use App\Models\Landlord\Invoice;
use App\Models\Landlord\Plan;
use App\Models\Landlord\User;
use App\Services\Landlord\Actions\Subscription\CreateSubscriptionService;
use App\Services\Landlord\Discount\DiscountCodeService;
use App\Services\Landlord\Invoice\InvoiceService;
use Illuminate\Support\Arr;

class PaidSubscriptionStrategy extends AbstractSubscriptionStrategy
{
    public function __construct(
        protected CreateSubscriptionService $createSubscriptionService,
        protected InvoiceService $invoiceService,
        protected readonly DiscountCodeService $discountCodeService
    ) {
        parent::__construct($createSubscriptionService, $invoiceService);
    }

    /**
     * @throws DiscountCodeException
     */
    public function validate(array $params, User $user): void
    {
        if (! isset($params['plan_id'], $params['duration_type'])) {
            throw new \InvalidArgumentException('Plan ID and duration are required for paid subscription');
        }
        // validate usage for tenant
        $discountCode = Arr::get($params, 'discountCode');
        if ($discountCode) {
            $is_used = DiscountCodeUsage::query()->where('discount_code_id', $discountCode->id)->where('tenant_id', $user->tenant_id)->exists();
            if ($is_used) {
                throw new DiscountCodeException('Discount code is already used for this tenant');
            }
        }
    }

    protected function buildSubscriptionDTO(array $params, User $user): SubscriptionDTO
    {
        $plan = Plan::query()->find($params['plan_id']);
        $duration = $params['duration_type'];
        $discountCode = Arr::get($params, 'discountCode');

        $invoiceDTO = $this->invoiceService->prepareForPaidSubscription(
            plan: $plan,
            duration: SubscriptionBillingCycleEnum::from($duration),
            user: $user,
            discountCode: $discountCode
        );
        $durationEnum = SubscriptionBillingCycleEnum::from($duration);

        return new SubscriptionDTO(
            plan_id: $plan->id,
            tenant_id: $user->tenant_id,
            starts_at: now(),
            amount: calculateSubscriptionAmount(plan: $plan, duration: $durationEnum),
            billing_cycle: $duration,
            ends_at: calculateSubscriptionEndDate(duration: $durationEnum),
            status: SubscriptionStatusEnum::PENDING->value,
            invoiceDTO: $invoiceDTO,
            shouldCreateInvoice: true
        );
    }

    protected function postProcess(array $params, User $user, ?Invoice $invoice): void
    {
        $discountCode = Arr::get($params, 'discountCode');
        if ($discountCode) {
            $discountCode->markAsUsed();
        }
    }

    public function getSubscriptionType(): string
    {
        return SubscriptionTypeEnum::PAID->value;
    }
}
