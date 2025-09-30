<?php

namespace App\Services;

use App\DTO\Form\FormDTO;
use App\Models\Tenant\Form;
use App\Models\Tenant\FormField;
use Illuminate\Support\Facades\DB;

class FormService extends BaseService
{

    public function __construct(
        public Form $model,
    ) {}

    public function getModel(): Form
    {
        return $this->model;
    }

    public function index(array $filters = [])
    {
        $forms = $this->model->with(['fields'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return $forms;
    }

    public function createForm(FormDTO $formDTO): Form
    {
        return DB::transaction(function () use ($formDTO) {
            // Create form
            $form = $this->model->create($formDTO->toArray());

            // Create fields
            $fieldsData = collect($formDTO->fields)->map(function ($field, $index) {
                return array_merge($field, ['order' => $field->order ?? $index]);
            });

            $this->processFormFields($form, $fieldsData);

            return $form->load(['fields']);
        });
    }

    public function show(int $id): Form
    {
        $form = $this->findById($id);
        return $form->load(['fields']);
    }

    public function update(FormDTO $formDTO, int $id): Form
    {
        $form = $this->findById($id);
        $form->update($formDTO->toArray());

        if ($formDTO->fields) {
            $form->fields()->delete();
            $form->fields()->createMany($formDTO->fields);
        }

        return $form->load(['fields']);
    }

    public function getRedirectUrl(Form $form): ?string
    {
        $redirectAction = $form->actions()->where('type', 'redirect')->first();
        return $redirectAction?->settings['url'] ?? null;
    }

    public function delete(int $id): bool
    {
        $form = $this->findById($id);
        $form->fields()->delete();
        return $form->delete();
    }

    public function processFormFields($form, $fieldsData)
    {
        $createdFields = [];
        $pendingFields = [];

        // First pass: Create independent fields
        foreach ($fieldsData as $fieldData) {
            if ($this->isIndependentField($fieldData)) {
                $field = $this->createFormField($form, $fieldData);
                $createdFields[$field->name] = $field->id;
                $createdFields[$field->id] = $field->id; // Also index by ID
            } else {
                $pendingFields[] = $fieldData;
            }
        }

        // Second pass: Process dependent fields
        $maxIterations = count($pendingFields) * 2; // Prevent infinite loops
        $iteration = 0;

        while (!empty($pendingFields) && $iteration < $maxIterations) {
            $remainingFields = [];

            foreach ($pendingFields as $fieldData) {
                $dependencyId = $this->resolveDependencyId($fieldData, $createdFields);

                if ($dependencyId !== null) {
                    $fieldData['depends_on_field_id'] = $dependencyId;
                    $field = $this->createFormField($form, $fieldData);
                    $createdFields[$field->name] = $field->id;
                    $createdFields[$field->id] = $field->id;
                } else {
                    $remainingFields[] = $fieldData;
                }
            }

            $pendingFields = $remainingFields;
            $iteration++;
        }

        // If we still have pending fields, there's a circular dependency
        if (!empty($pendingFields)) {
            throw new \Exception('Circular dependency detected in form fields');
        }
    }

    private function isIndependentField(array $fieldData): bool
    {
        return empty($fieldData['depends_on_field_id']) &&
            empty($fieldData['depends_on_field_name']);
    }

    private function resolveDependencyId(array $fieldData, array $createdFields): ?int
    {
        // Try to resolve by field name first
        if (!empty($fieldData['depends_on_field_name']) && isset($createdFields[$fieldData['depends_on_field_name']])) {
            return $createdFields[$fieldData['depends_on_field_name']];
        }

        // Try to resolve by field ID
        if (!empty($fieldData['depends_on_field_id']) && isset($createdFields[$fieldData['depends_on_field_id']])) {
            return $fieldData['depends_on_field_id'];
        }

        return null;
    }

    private function createFormField(Form $form, array $fieldData): FormField
    {
        $defaults = [
            'form_id' => $form->id,
            'is_conditional' => false,
            'condition_type' => 'equals',
            'required' => false,
            'order' => 0,
            'options' => null,
            'placeholder' => null,
            'depends_on_field_id' => null,
            'depends_on_value' => null,
        ];

        $fieldData = array_merge($defaults, $fieldData);

        // Auto-set is_conditional if dependency exists
        if (!empty($fieldData['depends_on_field_id'])) {
            $fieldData['is_conditional'] = true;
        }

        return FormField::create($fieldData);
    }
}
