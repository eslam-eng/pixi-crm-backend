<?php

namespace App\Http\Controllers\Api\Tasks;

use App\DTO\Tenant\TaskDTO;
use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Tasks\TaskRequest;
use App\Http\Requests\Tenant\Tasks\TaskChangeStatusRequest;
use App\Http\Resources\Tenant\Tasks\TaskResource;
use App\Http\Resources\Tenant\Tasks\TaskShowResource;
use App\Services\Tenant\Tasks\TaskService;
use DB;
use Exception;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(public TaskService $taskService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
    
            $filters = array_filter($request->all(), function ($value) {
                return ($value !== null && $value !== false && $value !== '');
            });
            $withRelations = [  'assignedTo.roles', 'priority.color'];
            $tasks = $this->taskService->paginate(filters: $filters,withRelations: $withRelations, limit: per_page());

            $data =  TaskResource::collection($tasks)->response()->getData(true);
            return apiResponse( $data, trans('app.data displayed successfully'));
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }
    public function store(TaskRequest $request)
    {
        try {
            DB::beginTransaction();
            $taskDTO = TaskDTO::fromRequest($request);
            $data = $this->taskService->store($taskDTO);
            DB::commit();
            return apiResponse(new TaskResource($data), trans('app.data created successfully'), code: 201);
        } catch (GeneralException $e) {
            DB::rollBack();
            return apiResponse(message: $e->getMessage(), code: 400);
        } catch (Exception $e) {
            DB::rollBack();
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function update(TaskRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $taskDTO = TaskDTO::fromRequest($request);
            $data = $this->taskService->update($id, $taskDTO);
            DB::commit();
            return apiResponse(new TaskResource($data), trans('app.data updated successfully'));
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
    public function show($id)
    {
        try {
            $task = $this->taskService->getModel()->with([
                'assignedTo.roles',
                'priority.color',
                'followers.roles',
                'reminders'
            ])->find($id);
            if (!$task) {
                return apiResponse(message: trans('app.data not found'), code: 404);
            }
            $data = new TaskShowResource($task);
            return apiResponse($data, trans('app.data displayed successfully'));
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Change task status
     */
    public function changeStatus(TaskChangeStatusRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = $this->taskService->changeStatus($id, $request->status);
            DB::commit();
            return apiResponse([], trans('app.data updated successfully'));
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
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $this->taskService->destroy($id);
            DB::commit();
            return apiResponse(message: trans('app.data deleted successfully'));
        } catch (GeneralException $e) {
            DB::rollBack();
            return apiResponse(message: $e->getMessage(), code: 400);
        } catch (Exception $e) {
            DB::rollBack();
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get task statistics
     */
    public function statistics()
    {
        try {
            $statistics = $this->taskService->getStatistics();
            return apiResponse($statistics, trans('app.data displayed successfully'));
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

 
}
