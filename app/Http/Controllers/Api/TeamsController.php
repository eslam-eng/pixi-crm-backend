<?php

namespace App\Http\Controllers\Api;

use App\DTO\Team\TeamDTO;
use App\Http\Resources\TeamResource;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teams\TeamRequest;
use App\Http\Resources\TeamDDLResource;
use App\Http\Resources\Tenant\Chairs\ChairResource;
use App\Models\Tenant\Chair;
use App\Services\TeamService;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class TeamsController extends Controller
{

    public function __construct(private readonly TeamService $teamService)
    {
        $this->middleware('permission:manage-settings')->except(['index']);
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = array_filter(request()->query());
            $withRelations = ['leader.roles', 'sales.roles'];
            if ($request->has('ddl')) {
                $teams = $this->teamService->index(filters: $filters, withRelations: $withRelations);
                $data = TeamDDLResource::collection($teams);
            } else {
                $teams = $this->teamService->index(filters: $filters, withRelations: $withRelations,  perPage: $filters['per_page'] ?? 10);
                $data = TeamResource::collection($teams)->response()->getData(true);
            }
            return ApiResponse($data, 'Teams retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function store(TeamRequest $request): JsonResponse
    {
        try {
            $teamDTO = TeamDTO::fromRequest($request);
            DB::beginTransaction();
            $team = $this->teamService->store($teamDTO);
            DB::commit();
            return ApiResponse(new TeamResource($team), 'Team created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function show($id)
    {
        try {
            $team = $this->teamService->findById(id: $id, withRelations: ['leader.roles', 'sales']);
            return ApiResponse(new TeamResource($team), 'Team retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(TeamRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $teamDTO = TeamDTO::fromRequest($request);
            $this->teamService->update($teamDTO, $id);
            DB::commit();
            return ApiResponse(message: trans('app.team_updated_successfully'), code: 200);
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->teamService->destroy(id: $id);
            return ApiResponse(message: trans('app.team_deleted_successfully'));
        } catch (NotFoundException $e) {
            return ApiResponse(message: $e->getMessage(), code: Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getChairs($id)
    {
        try {
            $chairs = Chair::where( 'team_id',$id)->with('user', 'monthlyTargets', 'quarterlyTargets')->get();
            return ApiResponse(ChairResource::collection($chairs), 'Team chairs retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
