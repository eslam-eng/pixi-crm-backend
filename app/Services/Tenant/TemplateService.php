<?php

namespace App\Services\Tenant;

use App\DTO\Tenant\Template\TemplateDTO;
use App\Exceptions\GeneralException;
use App\Models\Tenant\Template;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TemplateService extends BaseService
{
    public function __construct(
        private Template $model
    ) {}

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
    public function render(Template $template, array $variables = []): string
    {
        return $this->renderText($template->body, $variables);
    }

    /**
     * Render subject with variables
     */
    public function renderSubject(string $subject, array $variables = []): string
    {
        return $this->renderText($subject, $variables);
    }

    /**
     * Render text with variables
     */
    private function renderText(string $text, array $variables = []): string
    {
        foreach ($variables as $key => $value) {
            $text = str_replace('{{' . $key . '}}', $value, $text);
            $text = str_replace('{{ ' . $key . ' }}', $value, $text);
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
}
