<?php

namespace App\Http\Controllers\Api\Tasks;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Tenant\Tasks\TaskTypeResource;
use App\Services\Tenant\Tasks\TaskTypeService;
use DB;
use Exception;

class TaskTypeController extends Controller
{

    public function __construct(public TaskTypeService $taskTypeService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = $this->taskTypeService->getAll([]);
            $data = TaskTypeResource::collection($data);
            return apiResponse( $data, trans('app.data displayed successfully'));
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function setDefault(int $id)
    {
        try {
            DB::beginTransaction();
            $data = $this->taskTypeService->setDefault($id);
            DB::commit();
            return apiResponse(new TaskTypeResource($data), 'Priority set as default successfully');
        } catch (GeneralException $e) {
            DB::rollBack();
            return apiResponse(message: $e->getMessage(), code: 400);
        } catch (Exception $e) {
            DB::rollBack();
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
