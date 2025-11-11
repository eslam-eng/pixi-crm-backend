<?php

namespace App\Services\Central\Subscription\Strategies;

use App\DTO\Central\SubscriptionDTO;
use App\Enums\Landlord\SubscriptionBillingCycleEnum;
use App\Enums\Landlord\SubscriptionStatusEnum;
use App\Enums\Landlord\SubscriptionTypeEnum;
use App\Exceptions\VerificationCode\ActivationCodeException;
use App\Models\Central\ActivationCode;
use App\Models\Central\Invoice;
use App\Models\Central\User;
use Illuminate\Support\Arr;

class ActivationCodeSubscriptionStrategy extends AbstractSubscriptionStrategy
{
    /**
     * @throws ActivationCodeException
     */
    public function validate(array $params, User $user): void
    {
        $activation_code = Arr::get($params, 'activation_code');
        if (! $activation_code) {
            throw new ActivationCodeException('Activation code is required');
        }
    }

    protected function buildSubscriptionDTO(array $params, User $user): SubscriptionDTO
    {
        $activationCode = ActivationCode::with(['source', 'plan'])
            ->firstWhere('code', $params['activation_code']);

        $amount = ($activationCode->plan->lifetime_price * $activationCode->source->payout_percentage) / 100;

        $invoiceDTO = $this->invoiceService->prepareForActivationCode(
            activationCode: $activationCode,
            user: $user
        );

        return new SubscriptionDTO(
            plan_id: $activationCode->plan_id,
            tenant_id: $user->tenant_id,
            starts_at: now(),
            amount: $amount,
            billing_cycle: SubscriptionBillingCycleEnum::LIFETIME->value,
            status: SubscriptionStatusEnum::ACTIVE->value,
            activation_code_id: $activationCode->id,
            invoiceDTO: $invoiceDTO,
            shouldCreateInvoice: true
        );
    }

    protected function postProcess(array $params, User $user, ?Invoice $invoice): void
    {
        $activationCode = Arr::get($params, 'activation_code');
        ActivationCode::query()->where('code', $activationCode)->update(['redeemed_at' => now(), 'user_id' => $user->id]);
    }

    public function getSubscriptionType(): string
    {
        return SubscriptionTypeEnum::ACTIVATION_CODE->value;
    }

    /**
     * @throws ActivationCodeException
     */
}
