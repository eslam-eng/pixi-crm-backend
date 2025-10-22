<?php

namespace App\Services\Landlord\Actions\Subscription\Strategies;

use App\DTOs\Landlord\SubscriptionDTO;
use App\Exceptions\TrialException;
use App\Models\Landlord\Invoice;
use App\Models\Landlord\User;
use App\Services\Landlord\Actions\Subscription\CreateSubscriptionService;
use App\Services\Landlord\Actions\Subscription\Interfaces\SubscriptionStrategyInterface;
use App\Services\Landlord\Invoice\InvoiceService;
use Illuminate\Support\Facades\DB;

abstract class AbstractSubscriptionStrategy implements SubscriptionStrategyInterface
{
    public function __construct(
        protected CreateSubscriptionService $createSubscriptionService,
        protected InvoiceService $invoiceService
    ) {}

    /**
     * @throws TrialException
     * @throws \Throwable
     */
    public function handle(array $params, User $user): ?Invoice
    {
        $this->validate($params, $user);

        $subscriptionDTO = $this->buildSubscriptionDTO($params, $user);

        return DB::connection('landlord')->transaction(function () use ($subscriptionDTO, $params, $user) {
            $invoice = $this->createSubscriptionService->handle($subscriptionDTO); // always return InvoiceObject or null

            $this->postProcess(params: $params, user: $user, invoice: $invoice);

            return $invoice;
        });

    }

    abstract protected function buildSubscriptionDTO(array $params, User $user): SubscriptionDTO;

    // what should do after subscription created
    abstract protected function postProcess(array $params, User $user, ?Invoice $invoice);
}
