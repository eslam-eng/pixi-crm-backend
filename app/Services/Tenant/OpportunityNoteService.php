<?php

namespace App\Services\Tenant;

use App\Models\Tenant\OpportunityNote;
use Illuminate\Database\Eloquent\Collection;

class OpportunityNoteService
{
    public function __construct(protected OpportunityNote $model)
    {
    }

    public function getByOpportunityId(int $opportunityId): Collection
    {
        return $this->model->where('lead_id', $opportunityId)
            ->with(['creator'])
            ->latest()
            ->get();
    }

    public function create(array $data): OpportunityNote
    {
        if (isset($data['opportunity_id'])) {
            $data['lead_id'] = $data['opportunity_id'];
            unset($data['opportunity_id']);
        }

        if (!isset($data['user_id']) && auth()->check()) {
            $data['user_id'] = auth()->id();
        }

        return $this->model->create($data);
    }

    public function update(int $id, array $data): OpportunityNote
    {
        $note = $this->model->find($id);

        if (!$note) {
            throw new \Exception(trans('app.data not found'), 400);
        }

        $note->update($data);
        return $note;
    }

    public function delete(int $id): bool
    {
        $note = $this->model->findOrFail($id);
        return $note->delete();
    }
}
