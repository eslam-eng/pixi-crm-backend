<?php

namespace App\Services;

use App\DTO\Reason\ReasonDTO;
use App\Models\Reason;
use App\QueryFilters\ReasonFilters;
use Illuminate\Database\Eloquent\Builder;

class ReasonService extends BaseService
{
    public function __construct(
        public Reason               $model,
    ) {}

    public function getModel(): Reason
    {
        return $this->model;
    }

    public function getAll(array $filters = [])
    {
        return $this->queryGet($filters)->get();
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
        $reasons = $this->model->with($withRelations)->orderBy('id', 'desc');
        return $reasons->filter(new ReasonFilters($filters));
    }

    public function datatable(array $filters = [], array $withRelations = [])
    {
        $reasons = $this->getQuery()->with($withRelations);
        return $reasons->filter(new ReasonFilters($filters));
    }

    public function store(ReasonDTO $reasonDTO){
        $reason_data=$reasonDTO->toArray();
        $reaon=$this->model->create($reason_data);
        return $reaon;
    }

    public function update(ReasonDTO $reasonDTO,$id){
        $reason=$this->findById($id);
        $reason->update($reasonDTO->toArray());
        return true;
    }

    public function delete(int $id)
    {
        return $this->getQuery()->where('id', $id)->delete();
    }

}
