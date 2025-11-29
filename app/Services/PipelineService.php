<?php

namespace App\Services;

use App\DTO\Pipeline\PipelineDTO;
use App\Models\Tenant\Pipeline;
use App\QueryFilters\PipelineFilters;
use Illuminate\Database\Eloquent\Builder;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;


class PipelineService extends BaseService
{
    public function __construct(
        public Pipeline $model,
    ) {
    }

    public function getModel(): Pipeline
    {
        return $this->model;
    }

    public function getTableName(): string
    {
        return $this->getModel()->getTable();
    }

    public function listing(array $filters = [], array $withRelations = [], $perPage = 5): \Illuminate\Contracts\Pagination\CursorPaginator
    {
        return $this->queryGet(filters: $filters, withRelations: $withRelations)->cursorPaginate($perPage);
    }

    public function queryGet(array $filters = [], array $withRelations = []): Builder
    {
        $industries = $this->model->with($withRelations)->orderBy('id', 'desc');
        return $industries->filter(new PipelineFilters($filters));
    }

    public function datatable(array $filters = [], array $withRelations = [])
    {

        $industries = $this->getQuery()->with($withRelations);
        return $industries->filter(new PipelineFilters($filters));
    }

    public function index(array $filters = [], array $withRelations = [], ?int $perPage = null)
    {
        $query = $this->queryGet(filters: $filters, withRelations: $withRelations);
        if ($perPage) {
            return $query->paginate($perPage);
        }
        return $query->get();
    }

    public function store(PipelineDTO $pipelineDTO): Pipeline
    {
        $pipeline = $this->model->create([
            'name' => $pipelineDTO->name,
        ]);

        return $pipeline;
    }

    public function update(Pipeline $pipeline, PipelineDTO $pipelineDTO): Pipeline
    {
        $pipeline->update([
            'name' => $pipelineDTO->name,
        ]);
        return $pipeline;
    }

    public function show(int $id)
    {
        return $this->getQuery()->findOrFail($id)->load('stages');
    }


    public function delete(int $id)
    {
        $pipeline = $this->getQuery()->findOrFail($id);
        return $pipeline->delete();
    }

    public function updateDefault(int $id)
    {
        $selected_pipeline = $this->getQuery()->findOrFail($id);
        if (!$selected_pipeline->firstStage) {
            throw new \Exception('Pipeline must have at least one stage');
        }

        $this->getQuery()->update(['is_default' => 0]);
        $pipeline = $this->getQuery()->findOrFail($id);
        $pipeline->update([
            'is_default' => true,
        ]);
        return $pipeline;
    }

    public function getDefaultPipeline()
    {
        return $this->getQuery()->where('is_default', true)->first();
    }
}
