<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Opportunity\OpportunityNoteRequest;
use App\Http\Resources\Tenant\Opportunity\OpportunityNoteResource;
use App\Services\Tenant\OpportunityNoteService;

class OpportunityNoteController extends Controller
{
    public function __construct(protected OpportunityNoteService $service)
    {
    }

    public function index($opportunity_id)
    {
        $notes = $this->service->getByOpportunityId($opportunity_id);
        return ApiResponse::success(OpportunityNoteResource::collection($notes), 'Opportunity notes retrieved successfully');
    }

    public function store(OpportunityNoteRequest $request)
    {
        $data = $request->validated();
        $note = $this->service->create($data);
        return ApiResponse::success(new OpportunityNoteResource($note), 'Note created successfully');
    }

    public function update(OpportunityNoteRequest $request, $id)
    {
        try {
            $note = $this->service->update($id, $request->validated());
            return ApiResponse::success(new OpportunityNoteResource($note), 'Note updated successfully');
        } catch (\Exception $e) {
            return ApiResponse::error(message: $e->getMessage(), code: $e->getCode());
        }
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return ApiResponse::success(null, 'Note deleted successfully');
    }
}
