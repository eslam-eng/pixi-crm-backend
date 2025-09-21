<?php

namespace App\Services\Central;

use App\Models\Tier;
use App\DTO\Tier\TierDTO;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Models\TierModule;
use App\Models\Module;

class TierService extends BaseService
{
    public function __construct(public Tier $model) {}

    public function getModel(): Model
    {
        return $this->model;
    }

    public function store(TierDTO $tierDTO): Tier
    {
        $tierData = $tierDTO->toArray();
        $modules = $tierData['modules'] ?? [];
        unset($tierData['modules']); // Remove modules from tier data as it's stored separately
        
        $tier = $this->model->create($tierData);

        // Create tier_modules records using a single query
        if (!empty($modules)) {
            $tierModuleData = collect($modules)->map(function ($moduleData) use ($tier) {
                return [
                    'tier_id' => $tier->id,
                    'module_id' => $moduleData['module_id'],
                    'limit_value' => $moduleData['limit_value'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();
            
            TierModule::insert($tierModuleData);
        }

        return $tier;
    }

    public function update(TierDTO $tierDTO, int $id): Tier
    {
        $tier = $this->findById($id);
        $tierData = $tierDTO->toArray();
        $tier->update($tierData);
        return $tier;
    }

    public function delete(int $id): bool
    {
        $tier = $this->findById($id);
        
        // Check if tier has active subscriptions
        if ($tier->subscriptions()->exists()) {
            throw new \Exception('Cannot delete tier with active subscriptions');
        }
        
        return $tier->delete();
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->all();
    }

    public function findActive(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->where('status', 'active')->get();
    }

    public function findByAvailability(string $availability): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->where('availability', $availability)->get();
    }
}
