<?php

namespace App\Services;

use App\DTO\Stage\StageDTO;
use App\Models\Stage;
use App\QueryFilters\StageFilters;
use Illuminate\Database\Eloquent\Builder;


class StageService extends BaseService
{
    public function __construct(
        public PipelineService $pipelineService,
        public Stage $model,
    ) {
    }

    public function getModel(): Stage
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
        $query = $this->model->with($withRelations)->orderBy('id', 'desc');
        return $query->filter(new StageFilters($filters));
    }

    public function datatable(array $filters = [], array $withRelations = [])
    {
        $query = $this->getQuery()->with($withRelations);
        return $query->filter(new StageFilters($filters));
    }

    public function index(array $filters = [], array $withRelations = [], ?int $perPage = 10, $pipelineId)
    {
        $pipeline = $this->pipelineService->getQuery()->findOrFail($pipelineId);
        $query = $this->queryGet(filters: $filters, withRelations: $withRelations);
        if ($perPage) {
            return $query->where('pipeline_id', $pipeline->id)->paginate($perPage);
        }
        return $query->where('pipeline_id', $pipeline->id)->paginate($perPage);
    }

    public function store(StageDTO $stageDTO, $pipelineId): Stage
    {
        $pipeline = $this->pipelineService->getQuery()->findOrFail($pipelineId);
        $stage = $this->model->create([
            'name' => $stageDTO->name,
            'probability' => $stageDTO->probability,
            'pipeline_id' => $pipeline->id,
            'seq_number' => $pipeline->stages()->count() + 1,
        ]);

        return $stage->load('pipeline');
    }

    public function update(StageDTO $stageDTO, $stageId): Stage
    {
        $stage = $this->getQuery()->findOrFail($stageId);
        $stage->update([
            'name' => $stageDTO->name,
            'probability' => $stageDTO->probability,
        ]);

        $stage = $stage->load('pipeline')->first();
        return $stage;
    }

    public function show(int $id)
    {
        $stage = $this->getQuery()->findOrFail($id);
        return $stage->load('pipeline');
    }


    public function delete(int $id)
    {
        $stage = $this->getQuery()->findOrFail($id);
        return $stage->delete();
    }
}
