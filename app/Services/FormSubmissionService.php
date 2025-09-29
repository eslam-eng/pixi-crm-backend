<?php

namespace App\Services;

use App\Models\Tenant\Form;
use App\Models\Tenant\FormSubmission;

class FormSubmissionService extends BaseService
{
    public function __construct(
        public FormSubmission $model,
        public FormService $formService,
    ) {}

    public function getModel(): FormSubmission
    {
        return $this->model;
    }

    public function getFormBySlug(string $slug): Form
    {
        $form = $this->formService->getModel()->with(['fields'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();
        if (!$form) {
            throw new \Exception('Form not found');
        }
        return $form;
    }

    public function index(array $filters = [])
    {
        $forms = $this->model->with(['form'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return $forms;
    }

    public function submitForm(Form $form, array $data, string $ipAddress = null, string $userAgent = null): FormSubmission
    {
        $submission = $form->submissions()->create([
            'data' => $data,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent
        ]);

        $form->incrementSubmissions();

        return $submission;
    }

    public function buildValidationRules(Form $form): array
    {
        $rules = [];

        foreach ($form->fields as $field) {
            $fieldRules = [];

            if ($field->required) {
                $fieldRules[] = 'required';
            }

            switch ($field->type) {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'file':
                    $fieldRules[] = 'file';
                    $fieldRules[] = 'max:2048';
                    break;
            }

            if (!empty($fieldRules)) {
                $rules[$field->name] = $fieldRules;
            }
        }

        dd($rules);
        return $rules;
    }
}
