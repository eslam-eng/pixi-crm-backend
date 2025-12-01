<?php

namespace App\Http\Controllers\Api;

use App\DTO\Tenant\LeadDTO;
use App\DTO\Tenant\LogCallDTO;
use App\DTO\Tenant\Opportunity\ActivityLogDTO;
use App\Enums\OpportunityStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lead\LogCallRequest;
use App\Http\Requests\Tenant\Opportunity\ActivityLogReuest;
use App\Http\Requests\Tenant\Opportunity\OpportunityRequest;
use App\Http\Requests\Tenant\Opportunity\StatusRequest;
use App\Http\Resources\AuditOpportunityResource;
use App\Http\Resources\Opportunity\OpportunityResource;
use App\Http\Resources\Tenant\Opportunity\OpportunityDDLResource;
use App\Http\Resources\Tenant\Stage\StageWithOpportunityResource;
use App\Models\Tenant\Activity;
use App\Models\Tenant\Lead;
use App\Services\LeadService;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OpportunityController extends Controller
{
    public function __construct(public LeadService $leadService)
    {
        $this->middleware('permission:view-leads')->only(['index', 'show']);
        $this->middleware('permission:create-leads')->only(['store']);
        $this->middleware('permission:kanban-leads')->only(['kanbanList']);
        $this->middleware('permission:edit-leads')->only(['update']);
        $this->middleware('permission:delete-leads')->only(['destroy']);
        $this->middleware('permission:change-stage')->only(['changeStage']);
        $this->middleware('permission:get-activities-list')->only(['getActivitiesList']);
    }

    public function statistics()
    {
        $opportunities = Lead::count();
        $opportunities_deals_value = Lead::sum('deal_value');
        $opportunities_win_probability = Lead::avg('win_probability');
        return ApiResponse(message: 'Opportunities statistics retrieved successfully', code: 200, data: [
            'opportunities' => $opportunities,
            'opportunities_deals_value' => $opportunities_deals_value,
            'opportunities_win_probability' => $opportunities_win_probability,
        ]);
    }

    public function index(Request $request)
    {
        $filters = array_filter($request->all(), function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });
        if ($request->has('ddl')) {
            $opportunities = $this->leadService->index(
                $filters,
                ['contact.contactPhones', 'city', 'stage', 'items.itemable', 'variants.product', 'user', 'items.category.parent']
            );
            $data = OpportunityDDLResource::collection($opportunities);
        } else {
            $opportunities = $this->leadService->index(
                $filters,
                ['contact.contactPhones', 'city', 'stage', 'items.itemable', 'variants.product', 'user', 'items.category.parent'],
                $filters['per_page'] ?? 10
            );
            $data = OpportunityResource::collection($opportunities)->response()->getData(true);
        }
        return ApiResponse(message: 'Opportunities retrieved successfully', code: 200, data: $data);
    }

    public function kanbanList()
    {
        $stagesWithAuthUserLeads = $this->leadService->kanbanList();
        return ApiResponse(
            message: 'Opportunities kanban list retrieved successfully',
            data: StageWithOpportunityResource::collection($stagesWithAuthUserLeads)
        );
    }

    public function store(OpportunityRequest $request)
    {
        try {
            DB::beginTransaction();
            $leadDTO = LeadDTO::fromRequest($request);
            $lead = $this->leadService->store($leadDTO);
            DB::commit();
            return ApiResponse(message: 'Opportunity created successfully', code: Response::HTTP_CREATED, data: new OpportunityResource($lead));
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $opportunity = $this->leadService->show($id);
            return ApiResponse(new OpportunityResource($opportunity), 'Opportunity retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function update(OpportunityRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $opportunityDTO = LeadDTO::fromRequest($request);
            $opportunity = $this->leadService->update($id, $opportunityDTO);
            DB::commit();
            return ApiResponse(message: 'Opportunity updated successfully', data: new OpportunityResource($opportunity));
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->leadService->delete($id);
            return ApiResponse(message: 'Opportunity deleted successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function changeStage(Request $request, int $opportunity)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validate([
                'stage_id' => 'required|exists:stages,id',
            ]);
            $opportunity = $this->leadService->changeStage($opportunity, $validated['stage_id']);
            
            DB::commit();
            return ApiResponse(message: 'Opportunity stage changed successfully', code: 200, data: new OpportunityResource($opportunity));
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'Opportunity not found', code: 404);
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function changeStatus(StatusRequest $request, int $opportunity_id)
    {
        try {
            DB::beginTransaction();
            $this->leadService->changeStatus($opportunity_id, OpportunityStatus::from($request->validated('status')));
            DB::commit();
            return ApiResponse(message: 'Opportunity status changed successfully', code: 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'Opportunity not found', code: 404);
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function getActivitiesList($id)
    {
        try {
            $opportunity = Lead::findOrFail($id);
            $audits = $opportunity->audits()->with('user')->latest()->get();
            return ApiResponse(message: 'Opportunity activity list retrieved successfully', code: 200, data: AuditOpportunityResource::collection($audits));
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'Opportunity not found', code: 404);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function logCall(LogCallRequest $request, $opportunity)
    {
        try {
            $data = LogCallDTO::fromArray($request->validated());
            $this->leadService->logCall($opportunity, $data);
            return ApiResponse(message: 'Call logged successfully', code: 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'Opportunity not found', code: 404);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function AddActivityLog(int $opportunityId, ActivityLogReuest $request)
    {
        try {
            $data = ActivityLogDTO::fromRequest($request);
            $this->leadService->addActivityLog($opportunityId, $data);
            return ApiResponse(message: 'Activity log added successfully', code: 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'Opportunity not found', code: 404);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
