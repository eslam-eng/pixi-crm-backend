<?php

namespace App\Services\Tenant\Automation;

use App\Models\Tenant\AutomationTrigger;
use Illuminate\Support\Collection;

class AutomationTriggerService
{
    /**
     * Get all active automation triggers for dropdown
     */
    public function getDropdownOptions(): Collection
    {
        return AutomationTrigger::getDropdownOptions();
    }

    /**
     * Get all automation triggers
     */
    public function getAll(): Collection
    {
        return AutomationTrigger::active()
            ->ordered()
            ->get();
    }

    /**
     * Get automation trigger by key
     */
    public function getByKey(string $key): ?AutomationTrigger
    {
        return AutomationTrigger::where('key', $key)->first();
    }

    /**
     * Get automation trigger by ID
     */
    public function getById(int $id): ?AutomationTrigger
    {
        return AutomationTrigger::find($id);
    }

    /**
     * Create a new automation trigger
     */
    public function create(array $data): AutomationTrigger
    {
        return AutomationTrigger::create($data);
    }

    /**
     * Update an automation trigger
     */
    public function update(int $id, array $data): bool
    {
        $trigger = AutomationTrigger::find($id);
        
        if (!$trigger) {
            return false;
        }

        return $trigger->update($data);
    }

    /**
     * Delete an automation trigger
     */
    public function delete(int $id): bool
    {
        $trigger = AutomationTrigger::find($id);
        
        if (!$trigger) {
            return false;
        }

        return $trigger->delete();
    }

    /**
     * Toggle trigger active status
     */
    public function toggleActive(int $id): bool
    {
        $trigger = AutomationTrigger::find($id);
        
        if (!$trigger) {
            return false;
        }

        return $trigger->update(['is_active' => !$trigger->is_active]);
    }



    /**
     * Get trigger names in all languages
     */
    public function getMultilingualNames(int $id): ?array
    {
        $trigger = AutomationTrigger::find($id);
        
        if (!$trigger) {
            return null;
        }

        return $trigger->getTranslations('name');
    }
}
