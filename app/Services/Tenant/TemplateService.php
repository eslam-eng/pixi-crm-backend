<?php

namespace App\Services\Tenant;

use App\DTO\Tenant\Template\TemplateDTO;
use App\Exceptions\GeneralException;
use App\Models\Tenant\Template;
use App\Services\BaseService;
use App\Services\ContactService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TemplateService extends BaseService
{
    public function __construct(
        private Template $model
    ) {
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function index(array $filters = [], array $withRelations = [], int $perPage = 10): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->queryGet(filters: $filters, withRelations: $withRelations)->paginate($perPage);
    }

    public function queryGet(array $filters = [], array $withRelations = []): \Illuminate\Database\Eloquent\Builder
    {
        $query = $this->getModel()->with($withRelations);

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query;
    }

    public function store(TemplateDTO $templateDTO): Template
    {
        // Generate slug if not provided
        if (empty($templateDTO->slug)) {
            $templateDTO->slug = Str::slug($templateDTO->name);
        }

        // Check if slug already exists
        if ($this->model->where('slug', $templateDTO->slug)->exists()) {
            throw new GeneralException('Template with this slug already exists');
        }

        return $this->model->create($templateDTO->toArray());
    }

    public function update(TemplateDTO $templateDTO, int $id): bool
    {
        $template = $this->findById($id);

        // Generate slug if not provided
        if (empty($templateDTO->slug)) {
            $templateDTO->slug = Str::slug($templateDTO->name);
        }

        // Check if slug already exists (excluding current template)
        if ($this->model->where('slug', $templateDTO->slug)->where('id', '!=', $id)->exists()) {
            throw new GeneralException('Template with this slug already exists');
        }

        return $template->update($templateDTO->toArray());
    }

    public function destroy(int $id): bool
    {
        $template = $this->findById($id);
        return $template->delete();
    }

    /**
     * Render template with variables
     */
    public function render(Template $template, string $emailOrPhone, array $variables = []): string
    {
        return $this->renderText($template->body, $emailOrPhone, $variables);
    }

    /**
     * Render subject with variables
     */
    public function renderSubject(string $subject, string $emailOrPhone, array $variables = []): string
    {
        return $this->renderText($subject, $emailOrPhone, $variables);
    }

    /**
     * Render text with variables
     */
    private function renderText(string $text, string $emailOrPhone, array $variables = []): string
    {
        $mainkeys = [
            'first_name',
            'last_name',
            'email',
            'address',
            'state',
            'zip_code',
            'company_name',
            'job_title',
            'department',
            'campaign_name',
            'website',
            'industry',
            'company_size',
            'notes'
        ];

        $relations = [
            'source:id,name',
            'user:id,first_name,last_name',
            'city:id,name',
            'country:id,name',
            'phone'
        ];

        $contact = app(ContactService::class)
            ->queryGet(withRelations: $relations)
            ->select(
                'id',
                'source_id',
                'user_id',
                'email',
                'first_name',
                'last_name',
                'address',
                'state',
                'zip_code',
                'city_id',
                'country_id',
                'company_name',
                'job_title',
                'department',
                'status',
                'contact_method',
                'campaign_name',
                'website',
                'industry',
                'company_size',
                'notes'
            )->where(function ($q) use ($emailOrPhone) {
                $q->where('email', $emailOrPhone)
                    ->orWhereHas('phone', function ($query) use ($emailOrPhone) {
                        $query->where('phone', $emailOrPhone);
                    });
            })->first();

        if ($contact == null) {
            throw new NotFoundHttpException('Contact not found');
        }

        foreach ($contact->toArray() as $key => $value) {
            if (in_array($key, $mainkeys)) {
                $text = str_replace('{{' . $key . '}}', $value, $text);
                $text = str_replace('{{ ' . $key . ' }}', $value, $text);
                continue;
            }

            if ($key === 'source') {
                $text = str_replace('{{' . $key . '}}', $contact->source->name, $text);
                $text = str_replace('{{ ' . $key . ' }}', $contact->source->name, $text);
                continue;
            }
            if ($key === 'user') {
                $text = str_replace('{{' . $key . '}}', $contact->user->first_name, $text);
                $text = str_replace('{{ ' . $key . ' }}', $contact->user->first_name, $text);
                continue;
            }
            if ($key === 'city') {
                $text = str_replace('{{' . $key . '}}', $contact->city->name, $text);
                $text = str_replace('{{ ' . $key . ' }}', $contact->city->name, $text);
                continue;
            }
            if ($key === 'country') {
                $text = str_replace('{{' . $key . '}}', $contact->country->name, $text);
                $text = str_replace('{{ ' . $key . ' }}', $contact->country->name, $text);
                continue;
            }
            if ($key === 'phone') {
                $text = str_replace('{{' . $key . '}}', $contact->phone, $text);
                $text = str_replace('{{ ' . $key . ' }}', $contact->phone, $text);
                continue;
            }
        }
        return $text;
    }

    /**
     * Get template by slug
     */
    public function findBySlug(string $slug, string $type = 'email'): ?Template
    {
        return Template::findBySlug($slug, $type);
    }

    public function getContactVariablesKeys(array $search = []): array
    {
        $mainkeys = [
            'first_name',
            'last_name',
            'email',
            'address',
            'state',
            'zip_code',
            'company_name',
            'job_title',
            'department',
            'campaign_name',
            'website',
            'industry',
            'company_size',
            'notes'
        ];
        $relations = [
            'source',
            'user',
            'city',
            'country',
            'phone'
        ];

        return collect(array_merge($mainkeys, $relations))
            ->filter(fn($field) => str_contains($field, $search['search'] ?? ''))
            ->mapWithKeys(fn($field) => [
                $field => [
                    'label' => str($field)->replace('_', ' ')->title()->toString(),
                    'value' => '{{ ' . $field . ' }}',
                    'description' => "get the " . str($field)->replace('_', ' ')->title()->toString() . " of the contact",
                ]
            ])->toArray();
    }
}
