<?php

namespace App\Services\Central;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class BaseService
{
    /**
     * Get the query builder with optional filters.
     * Child classes must implement getFilterClass() to specify their filter.
     */
    public function getQuery(?array $filters = [], array $withRelation = []): ?Builder
    {
        $query = $this->baseQuery();
        $filterClass = $this->getFilterClass();
        if (! empty($filters) && class_exists($filterClass)) {
            $query = $query->filter(new $filterClass($filters));
        }

        return $query->with($withRelation);
    }

    /**
     * Child classes should return the filter class name.
     */
    abstract protected function getFilterClass(): ?string;

    /**
     * Child classes should return the base query (e.g., Model::query()).
     */
    abstract protected function baseQuery(): Builder;

    /**
     * Find a model by its primary key.
     *
     * @param  mixed  $id
     */
    public function findById($id, array $withRelation = []): ?Model
    {
        $model = $this->baseQuery()->with($withRelation)->find($id);
        if (! $model) {
            throw new NotFoundHttpException('resource not found');
        }

        return $model;
    }

    /**
     * Find a model by a given key and value.
     *
     * @param  mixed  $value
     */
    public function findByKey(string $key, string $value, array $withRelation = []): ?Model
    {
        return $this->baseQuery()->with($withRelation)->where($key, $value)->first();
    }

    // Add more shared methods as needed...
}
