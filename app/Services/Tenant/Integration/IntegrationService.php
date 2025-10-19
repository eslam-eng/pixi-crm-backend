<?php

namespace App\Services\Tenant\Integration;

use App\Models\Tenant\Integration;
use App\Enums\IntegrationStatusEnum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;

class IntegrationService
{
    /**
     * Get all integrations with optional filtering and pagination
     */
    public function index(array $filters = [], array $withRelations = [], ?int $perPage = null): Collection|LengthAwarePaginator
    {
        $query = Integration::query();

        // Apply filters
        if (!empty($filters)) {
            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }
            
            if (isset($filters['platform'])) {
                $query->where('platform', 'like', '%' . $filters['platform'] . '%');
            }
            
            if (isset($filters['is_active'])) {
                $query->where('is_active', $filters['is_active']);
            }
            
            if (isset($filters['name'])) {
                $query->where('name', 'like', '%' . $filters['name'] . '%');
            }
        }

        // Load relations if specified
        if (!empty($withRelations)) {
            $query->with($withRelations);
        }

        // Apply pagination if specified
        if ($perPage) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    /**
     * Find integration by ID
     */
    public function findById(int $id, array $withRelations = []): Integration
    {
        $query = Integration::query();

        if (!empty($withRelations)) {
            $query->with($withRelations);
        }

        $integration = $query->find($id);

        if (!$integration) {
            throw new Exception('Integration not found', 404);
        }

        return $integration;
    }

    /**
     * Create a new integration
     */
    public function store(array $data): Integration
    {
        return Integration::create($data);
    }

    /**
     * Update an existing integration
     */
    public function update(int $id, array $data): Integration
    {
        $integration = $this->findById($id);
        
        $integration->update($data);
        
        return $integration->fresh();
    }

    /**
     * Delete an integration
     */
    public function delete(int $id): bool
    {
        $integration = $this->findById($id);
        
        return $integration->delete();
    }

    /**
     * Toggle the active status of an integration
     */
    public function toggleStatus(int $id): Integration
    {
        $integration = $this->findById($id);
        
        $integration->update(['is_active' => !$integration->is_active]);
        
        return $integration->fresh();
    }

    /**
     * Update integration status
     */
    public function updateStatus(int $id, IntegrationStatusEnum $status): Integration
    {
        $integration = $this->findById($id);
        
        $integration->update(['status' => $status]);
        
        return $integration->fresh();
    }

    /**
     * Update last sync timestamp
     */
    public function updateLastSync(int $id): Integration
    {
        $integration = $this->findById($id);
        
        $integration->update(['last_sync' => now()]);
        
        return $integration->fresh();
    }

    /**
     * Get integration statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => Integration::count(),
            'active' => Integration::where('is_active', true)->count(),
            'connected' => Integration::where('status', IntegrationStatusEnum::CONNECTED)->count(),
            'disconnected' => Integration::where('status', IntegrationStatusEnum::DISCONNECTED)->count(),
            'error' => Integration::where('status', IntegrationStatusEnum::ERROR)->count(),
        ];
    }

    /**
     * Get integrations by status
     */
    public function getByStatus(IntegrationStatusEnum $status): Collection
    {
        return Integration::where('status', $status)->get();
    }

    /**
     * Get active integrations
     */
    public function getActiveIntegrations(): Collection
    {
        return Integration::where('is_active', true)->get();
    }

    /**
     * Get connected integrations
     */
    public function getConnectedIntegrations(): Collection
    {
        return Integration::where('status', IntegrationStatusEnum::CONNECTED)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Check if integration is connected
     */
    public function isConnected(int $id): bool
    {
        $integration = $this->findById($id);
        
        return $integration->isConnected();
    }

    /**
     * Validate integration credentials
     */
    public function validateCredentials(int $id): bool
    {
        $integration = $this->findById($id);
        
        return $integration->hasValidCredentials();
    }
}
