<?php

namespace App\Services;

use App\DTO\Form\FormDTO;
use App\Models\Tenant\Form;
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
            $form->fields()->createMany($fieldsData);

            return $form->load(['fields']);
        });
    }

    public function getRedirectUrl(Form $form): ?string
    {
        $redirectAction = $form->actions()->where('type', 'redirect')->first();
        return $redirectAction?->settings['url'] ?? null;
    }
}
