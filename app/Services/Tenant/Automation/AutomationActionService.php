<?php

namespace App\Services\Tenant\Automation;

use App\Models\Tenant\AutomationAction;
use Illuminate\Support\Collection;

class AutomationActionService
{
    /**
     * Get all active automation actions for dropdown
     * Filters by module_name if provided (returns actions with null module_name OR matching module_name)
     */
    public function getDropdownOptions(?string $moduleName = null): Collection
    {
        return AutomationAction::getDropdownOptions($moduleName);
    }

    /**
     * Get all automation actions
     */
    public function getAll(): Collection
    {
        return AutomationAction::active()
            ->ordered()
            ->get();
    }

    /**
     * Get automation action by key
     */
    public function getByKey(string $key): ?AutomationAction
    {
        return AutomationAction::where('key', $key)->first();
    }

    /**
     * Get automation action by ID
     */
    public function getById(int $id): ?AutomationAction
    {
        return AutomationAction::find($id);
    }

    /**
     * Create a new automation action
     */
    public function create(array $data): AutomationAction
    {
        return AutomationAction::create($data);
    }

    /**
     * Update an automation action
     */
    public function update(int $id, array $data): bool
    {
        $action = AutomationAction::find($id);

        if (!$action) {
            return false;
        }

        return $action->update($data);
    }

    /**
     * Delete an automation action
     */
    public function delete(int $id): bool
    {
        $action = AutomationAction::find($id);

        if (!$action) {
            return false;
        }

        return $action->delete();
    }

    /**
     * Toggle action active status
     */
    public function toggleActive(int $id): bool
    {
        $action = AutomationAction::find($id);

        if (!$action) {
            return false;
        }

        return $action->update(['is_active' => !$action->is_active]);
    }

    /**
     * Get available locales
     */
    public function getAvailableLocales(): array
    {
        return ['ar', 'en', 'fr', 'es'];
    }

    /**
     * Get action names in all languages
     */
    public function getMultilingualNames(int $id): ?array
    {
        $action = AutomationAction::find($id);

        if (!$action) {
            return null;
        }

        return $action->getTranslations('name');
    }
}
