<?php

namespace App\Services\Tenant\Automation;

use App\Enums\ContactStatus;
use App\Enums\OpportunityStatus;
use App\Enums\TaskStatusEnum;
use App\Models\City;
use App\Models\Country;
use App\Models\Source;
use App\Models\Stage;
use App\Models\Tenant\AutomationTrigger;
use App\Models\Tenant\AutomationTriggerField;
use App\Models\Tenant\Contact;
use App\Models\Tenant\Department;
use App\Models\Tenant\Lead;
use App\Models\Tenant\Priority;
use App\Models\Tenant\TaskType;
use App\Models\Tenant\User;
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

    /**
     * Get available fields for a specific trigger
     */
    public function getTriggerFields(int $triggerId): ?array
    {
        $trigger = AutomationTrigger::with('fields')->find($triggerId);

        if (!$trigger) {
            return null;
        }

        return [
            'trigger' => [
                'id' => $trigger->id,
                'key' => $trigger->key,
                'name' => $trigger->name,
                'description' => $trigger->description,
            ],
            'fields' => $trigger->fields->map(function ($field) {
                return [
                    'id' => $field->id,
                    'field_name' => $field->field_name,
                    'field_type' => $field->field_type,
                    'field_label' => $field->field_label,
                    'field_category' => $field->field_category,
                    'is_relationship' => $field->is_relationship,
                    'description' => $field->description,
                    'example_value' => $field->example_value,
                ];
            })->toArray(),
        ];
    }

    /**
     * Get options for a specific field
     */
    public function getFieldOptions(int $fieldId): ?array
    {
        $field = AutomationTriggerField::find($fieldId);

        if (!$field) {
            return null;
        }

        // Only return options for enum fields
        if ($field->field_type !== 'enum') {
            return [
                'field_id' => $field->id,
                'field_name' => $field->field_name,
                'field_type' => $field->field_type,
                'message' => 'This field does not have predefined options',
                'options' => [],
            ];
        }

        $options = $this->fetchFieldOptions($field);

        return [
            'field_id' => $field->id,
            'field_name' => $field->field_name,
            'field_label' => $field->field_label,
            'field_type' => $field->field_type,
            'field_category' => $field->field_category,
            'options' => $options,
        ];
    }

    /**
     * Fetch options based on field name and category
     */
    private function fetchFieldOptions(AutomationTriggerField $field): array
    {
        // Handle relationship fields
        if ($field->field_category === 'relationship') {
            return $this->fetchRelationshipOptions($field->field_name);
        }

        // Handle direct fields - pass the field for context
        return $this->fetchDirectFieldOptions($field->field_name, $field);
    }

    /**
     * Fetch options for relationship fields
     */
    private function fetchRelationshipOptions(string $fieldName): array
    {
        // Parse relationship field name (e.g., "source.name", "city.name")
        $parts = explode('.', $fieldName);

        if (count($parts) < 2) {
            return [];
        }

        $relation = $parts[0];
        $column = $parts[count($parts) - 1];

        // Map relationship names to models
        $modelMap = [
            'source' => Source::class,
            'city' => City::class,
            'country' => Country::class,
            'user' => User::class,
            'stage' => Stage::class,
            'priority' => Priority::class,
            'taskType' => TaskType::class,
            'contact' => Contact::class,
            'lead' => Lead::class,
            'assignedTo' => User::class,
            'assigned_to' => User::class,
        ];

        // Handle nested relationships (e.g., "contact.source.name")
        if (count($parts) > 2) {
            // For nested relationships, get the base model and traverse
            $baseRelation = $parts[0];
            $nestedRelation = $parts[1];

            if ($baseRelation === 'contact' && $nestedRelation === 'source') {
                return $this->fetchRelationshipOptions('source.name');
            }
            if ($baseRelation === 'lead' && $nestedRelation === 'contact') {
                return $this->fetchRelationshipOptions('contact.' . $column);
            }
            if ($baseRelation === 'lead' && $nestedRelation === 'status') {
                return $this->fetchDirectFieldOptions('status');
            }
        }

        if (!isset($modelMap[$relation])) {
            return [];
        }

        $modelClass = $modelMap[$relation];

        try {
            // Fetch distinct values from the related table
            $query = $modelClass::query();

            // Apply active scope if available
            if (method_exists($modelClass, 'scopeActive')) {
                $query->active();
            }

            // For status fields in relationships, handle specially
            if ($column === 'status' && $relation === 'lead') {
                return $this->fetchDirectFieldOptions('status');
            }

            $results = $query->select('id', $column)
                ->distinct()
                ->orderBy($column)
                ->get();

            return $results->map(function ($item) use ($column) {
                return [
                    'value' => $item->id,
                    'label' => $item->$column,
                ];
            })->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Fetch options for direct fields
     */
    private function fetchDirectFieldOptions(string $fieldName, ?AutomationTriggerField $triggerField = null): array
    {
        return match ($fieldName) {
            'status' => $this->getStatusOptions($triggerField),
            'department' => $this->getDepartmentOptions(),
            'priority_id' => $this->getPriorityOptions(),
            'task_type_id' => $this->getTaskTypeOptions(),
            'source_id' => $this->getSourceOptions(),
            'user_id' => $this->getUserOptions(),
            'assigned_to_id' => $this->getUserOptions(),
            'stage_id' => $this->getStageOptions(),
            'country_id' => $this->getCountryOptions(),
            'city_id' => $this->getCityOptions(),
            'contact_id' => $this->getContactOptions(),
            'lead_id' => $this->getLeadOptions(),
            'contact_method' => $this->getContactMethodOptions(),
            'payment_status' => $this->getPaymentStatusOptions(),
            'approval_status' => $this->getApprovalStatusOptions(),
            'discount_type' => $this->getDiscountTypeOptions(),
            default => [],
        };
    }

    /**
     * Get status options based on trigger context
     * 
     * @return array<int, array{value: string, label: string}>
     */
    private function getStatusOptions(?AutomationTriggerField $triggerField): array
    {
        $triggerKey = $triggerField?->automationTrigger?->key;

        if ($triggerKey && str_contains($triggerKey, 'contact')) {
            return $this->mapEnumToOptions(ContactStatus::cases());
        }

        if ($triggerKey && str_contains($triggerKey, 'task')) {
            return $this->mapEnumToOptions(TaskStatusEnum::cases());
        }

        if ($triggerKey && (str_contains($triggerKey, 'opportunity') || str_contains($triggerKey, 'lead'))) {
            return $this->mapEnumToOptions(OpportunityStatus::cases());
        }

        // Default to contact status
        return $this->mapEnumToOptions(ContactStatus::cases());
    }

    /**
     * Map enum cases to option array
     * 
     * @param array $cases
     * @return array<int, array{value: string, label: string}>
     */
    private function mapEnumToOptions(array $cases): array
    {
        return array_map(fn($value) => [
            'value' => $value->value,
            'label' => $value->label(),
        ], $cases);
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function getDepartmentOptions(): array
    {
        return Department::active()
            ->get(['id', 'name'])
            ->map(fn($dept) => [
                'value' => $dept->name,
                'label' => $dept->name,
            ])->toArray();
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function getPriorityOptions(): array
    {
        return Priority::ordered()
            ->get(['id', 'name'])
            ->map(fn($priority) => [
                'value' => $priority->id,
                'label' => $priority->name,
            ])->toArray();
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function getTaskTypeOptions(): array
    {
        return TaskType::ordered()
            ->get(['id', 'name'])
            ->map(fn($type) => [
                'value' => $type->id,
                'label' => $type->name,
            ])->toArray();
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function getSourceOptions(): array
    {
        return Source::all(['id', 'name'])
            ->map(fn($source) => [
                'value' => $source->id,
                'label' => $source->name,
            ])->toArray();
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function getUserOptions(): array
    {
        $currentUser = auth('api_tenant')->user();
        if (!$currentUser) {
            return [];
        }

        $query = User::query();

        if ($currentUser->hasRole('admin')) {
            // Admin sees all users
        } elseif ($currentUser->can('view_manager_dashboard')) {
            $query->where('team_id', $currentUser->team_id);
        } elseif ($currentUser->can('view_agent_dashboard')) {
            $query->where('id', $currentUser->id);
        } else {
            $query->where('id', $currentUser->id);
        }

        return $query->get(['id', 'first_name', 'last_name'])
            ->map(fn($user) => [
                'value' => $user->id,
                'label' => trim($user->first_name . ' ' . $user->last_name),
            ])->toArray();
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function getStageOptions(): array
    {
        return Stage::all(['id', 'name'])
            ->map(fn($stage) => [
                'value' => $stage->id,
                'label' => $stage->name,
            ])->toArray();
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function getCountryOptions(): array
    {
        return Country::all(['id', 'name'])
            ->map(fn($country) => [
                'value' => $country->id,
                'label' => $country->name,
            ])->toArray();
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function getCityOptions(): array
    {
        return City::all(['id', 'name'])
            ->map(fn($city) => [
                'value' => $city->id,
                'label' => $city->name,
            ])->toArray();
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function getContactOptions(): array
    {
        return Contact::all(['id', 'first_name', 'last_name', 'email'])
            ->map(function ($contact) {
                $label = trim($contact->first_name . ' ' . $contact->last_name);
                if ($contact->email) {
                    $label .= ' (' . $contact->email . ')';
                }
                return [
                    'value' => $contact->id,
                    'label' => $label,
                ];
            })->toArray();
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function getLeadOptions(): array
    {
        return Lead::all(['id', 'status'])
            ->map(fn($lead) => [
                'value' => $lead->id,
                'label' => 'Lead #' . $lead->id . ' - ' . $lead->status,
            ])->toArray();
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function getContactMethodOptions(): array
    {
        return [
            ['value' => 'email', 'label' => 'Email'],
            ['value' => 'phone', 'label' => 'Phone'],
            ['value' => 'whatsapp', 'label' => 'WhatsApp'],
        ];
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function getPaymentStatusOptions(): array
    {
        return [
            ['value' => 'pending', 'label' => 'Pending'],
            ['value' => 'partial', 'label' => 'Partial'],
            ['value' => 'paid', 'label' => 'Paid'],
            ['value' => 'overdue', 'label' => 'Overdue'],
        ];
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function getApprovalStatusOptions(): array
    {
        return [
            ['value' => 'pending', 'label' => 'Pending'],
            ['value' => 'approved', 'label' => 'Approved'],
            ['value' => 'rejected', 'label' => 'Rejected'],
        ];
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function getDiscountTypeOptions(): array
    {
        return [
            ['value' => 'percentage', 'label' => 'Percentage'],
            ['value' => 'fixed', 'label' => 'Fixed'],
        ];
    }
}
