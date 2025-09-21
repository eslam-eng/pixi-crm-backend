<?php

namespace App\Http\Controllers\Api\Tasks;

use App\Exceptions\GeneralException;
use Exception;
use Illuminate\Http\Request;
use App\DTO\Priority\PriorityDTO;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Tasks\PriorityRequest;
use App\Http\Resources\Tenant\Tasks\PriorityResource;
use App\Services\Tenant\Tasks\PriorityService;

class PriorityController extends Controller
{
    public function __construct(public PriorityService $priorityService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $filters = array_filter($request->get('filters', []), function ($value) {
                return ($value !== null && $value !== false && $value !== '');
            });
            $priorities = $this->priorityService->getAll($filters);
            $data =  PriorityResource::collection($priorities);
            return apiResponse($data, trans('app.data displayed successfully'));
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PriorityRequest $request)
    {
        try {
            DB::beginTransaction();
            $priorityDTO = PriorityDTO::fromRequest($request);
            $priority = $this->priorityService->store($priorityDTO);
            DB::commit();
            return apiResponse(new PriorityResource($priority), 'Priority created successfully', code: 201);
        } catch (GeneralException $e) {
            DB::rollBack();
            return apiResponse(message: $e->getMessage(), code: 400);
        } catch (Exception $e) {
            DB::rollBack();
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try {
            $priority = $this->priorityService->findById($id);
            return apiResponse(new PriorityResource($priority), 'Priority retrieved successfully');
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PriorityRequest $request, int $id)
    {
        try {
            DB::beginTransaction();
            $priorityDTO = PriorityDTO::fromRequest($request);
            $priority = $this->priorityService->update($id, $priorityDTO);
            DB::commit();
            return apiResponse(new PriorityResource($priority), 'Priority updated successfully');
        } catch (GeneralException $e) {
            DB::rollBack();
            return apiResponse(message: $e->getMessage(), code: 400);
        } catch (Exception $e) {
            DB::rollBack();
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {

        try {
            DB::beginTransaction();
            $this->priorityService->destroy($id);
            DB::commit();
            return apiResponse(null, 'Priority deleted successfully');
        } catch (GeneralException $e) {
            DB::rollBack();
            return apiResponse(message: $e->getMessage(), code: 400);
        } catch (Exception $e) {
            DB::rollBack();
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Set a priority as default.
     */
    public function setDefault(int $id)
    {
        try {
            DB::beginTransaction();
            $priority = $this->priorityService->setDefault($id);
            DB::commit();
            return apiResponse(new PriorityResource($priority), 'Priority set as default successfully');
        } catch (GeneralException $e) {
            DB::rollBack();
            return apiResponse(message: $e->getMessage(), code: 400);
        } catch (Exception $e) {
            DB::rollBack();
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get the default priority.
     */
    public function getDefault()
    {
        try {
            $priority = $this->priorityService->getDefault();
            if (!$priority) {
                return apiResponse(null, 'No default priority found', code: 404);
            }
            return apiResponse(new PriorityResource($priority), 'Default priority retrieved successfully');
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
