<?php

namespace App\Services\Central\Subscription\Strategies;

use App\DTO\Central\SubscriptionDTO;
use App\Exceptions\TrialException;
use App\Models\Central\Invoice;
use App\Models\Central\User;
use App\Services\Central\Subscription\CreateSubscriptionService;
use App\Services\Central\Subscription\Interfaces\SubscriptionStrategyInterface;
use App\Services\Central\Invoice\InvoiceService;
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

        return DB::connection('Central')->transaction(function () use ($subscriptionDTO, $params, $user) {
            $invoice = $this->createSubscriptionService->handle($subscriptionDTO); // always return InvoiceObject or null

            $this->postProcess(params: $params, user: $user, invoice: $invoice);

            return $invoice;
        });
    }

    abstract protected function buildSubscriptionDTO(array $params, User $user): SubscriptionDTO;

    // what should do after subscription created
    abstract protected function postProcess(array $params, User $user, ?Invoice $invoice);
}
