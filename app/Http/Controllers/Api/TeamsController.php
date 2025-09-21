<?php

namespace App\Http\Controllers\Api;

use App\DTO\Team\TeamDTO;
use App\Http\Requests\Teams\TeamStoreRequest;
use App\Http\Resources\TeamCollection;
use App\Http\Resources\TeamResource;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\UserUpdateRequest;
use App\Http\Requests\Users\UserUpdateProfileRequest;
use App\Services\TeamService;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeamsController extends Controller
{

    public function __construct(private readonly TeamService $teamService) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->query('per_page');
            $filters = array_filter(request()->query());
            $withRelations = ['leader', 'location', 'source', 'sales'];
            $teams = $this->teamService->index(filters: $filters, withRelations: $withRelations,  perPage: $perPage);
            return ApiResponse(new TeamCollection($teams), 'Teams retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function store(TeamStoreRequest $request): JsonResponse
    {
        try {
            $teamDTO = TeamDTO::fromRequest($request);
            DB::beginTransaction();
            $team = $this->teamService->store($teamDTO);
            DB::commit();
            return ApiResponse(new TeamResource($team), 'Team created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            dd($e);
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        try {
            $user = $this->teamService->findById(id: $id);
            return view('layouts.dashboard.users.show', compact('user'));
        } catch (Exception $e) {
            $toast = [
                'type' => 'error',
                'title' => 'error',
                'message' => trans('app.there_is_an_error')
            ];
            return back()->with('toast', $toast);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        try {
            $user = $this->teamService->findById(id: $id);
            return view('layouts.dashboard.users.edit', compact('user'));
        } catch (Exception $e) {
            return redirect()->back();
        }
    }

    /**
     * @param UserUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserUpdateRequest $request, $id)
    {
        try {
            $userDTO = $request->toUserDTO();
            $this->teamService->update($userDTO, $id);
            $toast = [
                'type' => 'success',
                'title' => 'success',
                'message' => trans('app.user_updated_successfully')
            ];
            return to_route('users.index')->with('toast', $toast);
        } catch (Exception $e) {
            dd($e);
            $toast = [
                'type' => 'error',
                'title' => 'error',
                'message' => trans('app.there_is_an_error')
            ];
            return back()->with('toast', $toast);
        }
    }

    public function updateProfile(UserUpdateProfileRequest $request, $id)
    {
        try {
            $this->teamService->updateProfile($request->validated(), $id);
            $toast = [
                'type' => 'success',
                'title' => 'success',
                'message' => trans('app.success_operation')
            ];
            return to_route('home')->with('toast', $toast);
        } catch (Exception $e) {
            $toast = [
                'type' => 'error',
                'title' => 'error',
                'message' => trans('app.there_is_an_error')
            ];
            return back()->with('toast', $toast);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->teamService->destroy(id: $id);
            return apiResponse(message: trans('lang.success_operation'));
        } catch (NotFoundException $e) {
            return apiResponse(message: $e->getMessage(), code: 422);
        } catch (Exception $e) {
            return apiResponse(message: trans('lang.something_went_wrong'), code: 422);
        }
    }
}
