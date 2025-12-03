<?php

namespace App\Http\Controllers\Api\Tasks;

use App\DTO\Tenant\TaskDTO;
use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Tasks\TaskRequest;
use App\Http\Requests\Tenant\Tasks\TaskChangeStatusRequest;
use App\Http\Requests\Tenant\Tasks\TaskCalendarRequest;
use App\Http\Resources\Tenant\Tasks\TaskResource;
use App\Http\Resources\Tenant\Tasks\TaskShowResource;
use App\Services\Tenant\Tasks\TaskService;
use DB;
use Exception;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(public TaskService $taskService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $filters = array_filter($request->all(), function ($value) {
                return ($value !== null && $value !== false && $value !== '');
            });
            $withRelations = ['assignedTo.roles', 'priority.color'];
            $tasks = $this->taskService->paginate(filters: $filters, withRelations: $withRelations, limit: per_page());

            $data = TaskResource::collection($tasks)->response()->getData(true);
            return apiResponse($data, trans('app.data displayed successfully'));
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

    /**
     * Get tasks for calendar view
     */
    public function calendar(TaskCalendarRequest $request)
    {
        try {
            $filters = $request->validated();

            // Build date filters based on calendar_type
            if ($filters['calendar_type'] === 'month') {
                $year = $filters['year'];
                $month = str_pad($filters['month'], 2, '0', STR_PAD_LEFT);
                $startDate = "$year-$month-01";
                $endDate = date('Y-m-t', strtotime($startDate));
                $filters['due_date_range'] = ['start' => $startDate, 'end' => $endDate];
                unset($filters['month'], $filters['year']);
            }
            // For week, due_date_range is already set

            unset($filters['calendar_type']);

            // Remove null/empty values
            $filters = array_filter($filters, function ($value) {
                return ($value !== null && $value !== false && $value !== '');
            });

            $withRelations = ['assignedTo.roles', 'priority.color', 'taskType', 'lead.contact'];
            $tasks = $this->taskService->getAll(filters: $filters, withRelations: $withRelations);

            $data = TaskResource::collection($tasks);
            return apiResponse($data, trans('app.data displayed successfully'));
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }


}
