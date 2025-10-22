<?php

namespace App\Services\Landlord\Plan;

use App\DTOs\Landlord\PlanDTO;
use App\Enum\SupportedLocalesEnum;
use App\Models\Landlord\Filters\PlanFilters;
use App\Models\Landlord\Plan;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PlanService extends BaseService
{
    protected function getFilterClass(): ?string
    {
        return PlanFilters::class;
    }

    protected function baseQuery(): Builder
    {
        return Plan::query();
    }

    public function getFreePlan()
    {
        return $this->getQuery()->trial()->first();
    }

    public function statics()
    {
        return $stats = $this->getQuery()
            ->selectRaw('
                            AVG(
                                (monthly_price + (annual_price / 12) + (lifetime_price / 36)) / 3
                            ) AS avg_price,
                            COUNT(*) AS total_plans,
                            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) AS active_plans')
            ->first();
    }

    public function paginate(array $filters = [], array $withRelation = [])
    {
        return $this->getQuery(filters: $filters, withRelation: $withRelation)
            ->orderBy('id')
            ->orderBy('sort_order')
            ->paginate();
    }

    public function activePlans(array $filters = [], array $withRelation = [])
    {
        return $this->getQuery(filters: $filters, withRelation: $withRelation)
            ->orderBy('id')
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * @throws \Throwable
     */
    public function create(PlanDTO $planDTO)
    {
        return DB::connection('landlord')->transaction(function () use ($planDTO) {
            $planData = $planDTO->toArray();
            $planData['name'] = [];
            foreach (SupportedLocalesEnum::values() as $locale) {
                $planData['name'][$locale] = $planDTO->name; // or provide translation per locale
            }
            $plan = $this->getQuery()->create($planData);

            $allFeaturesToAttach = $this->prepareFeaturesAndLimits($planDTO);
            $plan->features()->attach($allFeaturesToAttach);

            return $plan;
        });
    }

    /**
     * @throws \Throwable
     */
    public function update(PlanDTO $planDTO, int $plan)
    {
        $plan = $this->findById($plan);

        return DB::connection('landlord')->transaction(function () use ($planDTO, $plan) {
            $planData = $planDTO->toArrayExcept(['features', 'limits']);
            $planData['name'] = [];
            foreach (SupportedLocalesEnum::values() as $locale) {
                $planData['name'][$locale] = $planDTO->name; // or provide translation per locale
            }
            $plan->update($planData);

            $allFeaturesToAttach = $this->prepareFeaturesAndLimits($planDTO);

            $plan->features()->sync($allFeaturesToAttach);

            return $plan;
        });
    }

    /**
     * Prepare combined features and limits array for sync/attach
     */
    private function prepareFeaturesAndLimits(PlanDTO $planDTO): array
    {
        //        $features = collect($planDTO->features ?? [])->mapWithKeys(function ($value, $id) {
        //            return [
        //                $id => [
        //                    'value' => $value ?? null,
        //                ]
        //            ];
        //        })->all();

        return collect($planDTO->limits ?? [])->mapWithKeys(function ($value, $id) {
            return [
                $id => [
                    'value' => $value,
                ],
            ];
        })->all();
    }

    public function delete(int $plan_id): ?bool
    {
        $plan = $this->findById($plan_id);

        return $plan->delete();
    }
}
