<?php

namespace App\Services\Central;

use App\DTO\Central\PlanDTO;
use App\Enums\Landlord\FeatureGroupEnum;
use App\Enums\Landlord\SupportedLocalesEnum;
use App\Models\Central\Plan;
use App\QueryFilters\PlanFilters;
use App\Services\Central\BaseService;
use App\Services\Central\FeatureService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PlanService extends BaseService
{
    public function __construct(
        public FeatureService $featureService,
    ) {
    }

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
            ->orderBy('id','desc')
            ->paginate(per_page());
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
        $this->validateFeatures($planDTO->features ?? []);
        return collect($planDTO->features ?? [])->mapWithKeys(function ($value, $id) {
            return [
                $value['id'] => [
                    'value' => $value['value'] ?? null,
                ]
            ];
        })->all();

        // return collect($planDTO->limits ?? [])->mapWithKeys(function ($value, $id) {
        //     return [
        //         $id => [
        //             'value' => $value,
        //         ],
        //     ];
        // })->all();
    }

    public function delete(int $plan_id): ?bool
    {
        $plan = $this->findById($plan_id);

        return $plan->delete();
    }

    private function validateFeatures(array $features): void
    {
        $ids = collect($features)->pluck('id')->toArray();

        $definitions = $this->featureService->getQuery()->select('id', 'group')->whereIn('id', $ids)->get();

        foreach ($features as $index => $item) {

            $id = $item['id'];
            $value = $item['value'];

            $feature = $definitions->where('id', $id)->first();

            if (!$this->validateType($value, $feature->group)) {
                throw ValidationException::withMessages([
                    "features.$index.value" =>
                        "Invalid value type for [$id]",
                ]);
            }
        }
    }

    protected function validateType(mixed $value, int $type): bool
    {
        return match ($type) {
            FeatureGroupEnum::LIMIT->value =>
            filter_var($value, FILTER_VALIDATE_INT) !== false,

            FeatureGroupEnum::FEATURE->value =>
            in_array($value, [0, 1, '0', '1', true, false], true),

            FeatureGroupEnum::STRING->value =>
            is_string($value),

            default => false,
        };
    }
}
