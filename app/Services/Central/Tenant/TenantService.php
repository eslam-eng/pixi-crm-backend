<?php

namespace App\Services\Central\Tenant;

use App\DTO\Central\TenantDTO;
use App\Enums\Landlord\SubscriptionStatusEnum;
use App\Models\Central\Filters\TenantFilters;
use App\Models\Central\Tenant;
use App\Models\Central\User;
use App\Services\Central\BaseService;
use App\Services\Central\Subscription\SubscriptionService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class TenantService extends BaseService
{
    public function __construct(protected readonly SubscriptionService $planSubscriptionService) {}

    protected function getFilterClass(): ?string
    {
        return TenantFilters::class;
    }

    protected function baseQuery(): Builder
    {
        return Tenant::query();
    }

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return $this->getQuery($filters)
            ->withCount('users')
            ->with(['owner', 'activeSubscription.plan:id,name'])
            ->paginate();
    }

    public function statics(): array
    {
        $countsGroupedByStatus = $this->planSubscriptionService->staticsByStatus();
        $trialCount = $countsGroupedByStatus
            ->where('status', SubscriptionStatusEnum::TRIAL)
            ->first()?->total ?? 0;

        $totalActiveAndTrial = $countsGroupedByStatus
            ->whereIn('status', [SubscriptionStatusEnum::ACTIVE, SubscriptionStatusEnum::TRIAL])
            ->sum('total');

        return [
            'trial_count' => $trialCount,
            'active_count' => $totalActiveAndTrial,
            'total_users' => User::query()->count(),
        ];
    }

    public function create(TenantDTO $dto): Tenant
    {
        return $this->getQuery()->create($dto->toArray());
    }

    public function update(int $id, TenantDTO $dto): Model
    {
        $tenant = $this->findById($id);
        $tenant->update($dto->toArray());

        return $tenant;
    }

    public function details($tenant_id): ?Model
    {
        $withRelations = [
            'owner',
            'activeSubscription' => fn($q) => $q->with('features'),
            'subscriptions' => fn($q) => $q->whereNotIn('status', [SubscriptionStatusEnum::ACTIVE->value, SubscriptionStatusEnum::TRIAL->value]),
            'invoices',
        ];

        return $this->findById($tenant_id, $withRelations);
    }

    public function delete(int $id): bool
    {
        $tenant = $this->findById($id);

        return $tenant->delete();
    }
}
