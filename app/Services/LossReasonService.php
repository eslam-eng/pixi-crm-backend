<?php

namespace App\Services;

use App\DTO\LossReason\LossReasonDTO;
use App\Models\Tenant\LossReason;
use App\QueryFilters\LossReasonFilters;
use Illuminate\Database\Eloquent\Builder;


class LossReasonService extends BaseService
{
    public function __construct(
        public PipelineService  $pipelineService,
        public LossReason  $model,
    ) {}

    public function getModel(): LossReason
    {
        return $this->model;
    }

    public function getTableName(): String
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
        return $industries->filter(new LossReasonFilters($filters));
    }

    public function datatable(array $filters = [], array $withRelations = [])
    {

        $industries = $this->getQuery()->with($withRelations);
        return $industries->filter(new LossReasonFilters($filters));
    }

    public function index(array $filters = [], array $withRelations = [], ?int $perPage = null, $pipelineId)
    {
        $pipeline = $this->pipelineService->getQuery()->findOrFail($pipelineId);
        $query = $this->queryGet(filters: $filters, withRelations: $withRelations);
        if ($perPage) {
            return $query->where('pipeline_id', $pipeline->id)->paginate($perPage);
        }
        return $query->where('pipeline_id', $pipeline->id)->get();
    }

    public function store(LossReasonDTO $lossReasonDTO, $pipelineId): LossReason
    {
        $pipeline = $this->pipelineService->getQuery()->findOrFail($pipelineId);
        $lossReason = $this->model->create([
            'label' => $lossReasonDTO->label,
            'value' => $lossReasonDTO->value,
            'description' => $lossReasonDTO->description,
            'pipeline_id' => $pipeline->id,
        ]);

        return $lossReason->load('pipeline');
    }

    public function update(LossReasonDTO $lossReasonDTO, $lossReasonId): LossReason
    {
        $lossReason = $this->getQuery()->findOrFail($lossReasonId);
        $lossReason->update([
            'label' => $lossReasonDTO->label,
            'value' => $lossReasonDTO->value,
            'description' => $lossReasonDTO->description,
        ]);
        return $lossReason->load('pipeline');
    }

    public function show(int $id)
    {
        $lossReason = $this->getQuery()->findOrFail($id);
        return $lossReason->load('pipeline');
    }


    public function delete(int $id)
    {
        $lossReason = $this->getQuery()->findOrFail($id);
        return $lossReason->delete();
    }
}
