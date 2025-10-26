<?php

namespace App\Http\Controllers\Api\Users;

use App\DTO\Tenant\AssignToTeam\AssignToTeamDTO;
use App\DTO\Tenant\UserDTO;
use App\Exceptions\GeneralException;
use App\Http\Requests\Tenant\Users\AssignToTeamRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Services\Tenant\Users\UserService;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Users\UserRequest;
use App\Http\Requests\Tenant\Users\UserUpdateProfileRequest;
use App\Http\Requests\Tenant\Users\ChangeLanguageRequest;
use App\Http\Resources\Tenant\Users\UserDDLResource;
use App\Http\Resources\Tenant\Users\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService)
    {
        $this->middleware('permission:manage-settings')->except(['index', 'show']);
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = array_filter(request()->query());
            $withRelations = ['roles', 'team'];
            if ($request->has('ddl')) {
                $users = $this->userService->index(filters: $filters);
                $data = UserDDLResource::collection($users);
            } else {
                $users = $this->userService->index(filters: $filters, withRelations: $withRelations,  perPage: $filters['per_page'] ?? 10);
                $data = UserResource::collection($users)->response()->getData(true);
            }
            return ApiResponse($data, 'Users retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function store(UserRequest $request): JsonResponse
    {
        try {
            $userDTO = UserDTO::fromRequest($request);
            DB::beginTransaction();
            $user = $this->userService->store($userDTO);
            DB::commit();
            return ApiResponse(new UserResource($user), 'User created successfully');
        } catch (ValidationException $e) {
            DB::rollBack();
            return ApiResponse(message: $e->errors(), code: 422);
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function show($id)
    {
        try {
            $user = $this->userService->getModel()->with(['roles', 'team'])->find($id);
            if (!$user) {
                return apiResponse(message: trans('app.data not found'), code: 404);
            }
            $data = new UserResource($user);
            return apiResponse($data, trans('app.data displayed successfully'));
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function update(UserRequest $request, $id): JsonResponse
    {
        try {
            $userDTO = UserDTO::fromRequest($request);
            DB::beginTransaction();
            $user = $this->userService->update($userDTO, $id);
            DB::commit();
            return ApiResponse(UserResource::make($user), 'User updated successfully');
        } catch (ValidationException $e) {
            DB::rollBack();
            return ApiResponse(message: $e->errors(), code: 422);
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function updateProfile(UserUpdateProfileRequest $request, $id)
    {
        try {
            $this->userService->updateProfile($request->validated(), $id);
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

    public function destroy($id)
    {
        try {
            $this->userService->destroy(id: $id);
            return apiResponse(message: trans('app.data deleted successfully'));
        } catch (NotFoundException $e) {
            return apiResponse(message: $e->getMessage(), code: 422);
        } catch (Exception $e) {
            return apiResponse(message: trans('lang.something_went_wrong'), code: 422);
        }
    }

    public function toggleStatus($id): JsonResponse
    {
        try {
            $this->userService->toggleStatus($id);
            $user = $this->userService->findById($id);
            $status = $user->is_active ? 'activated' : 'deactivated';
            return ApiResponse([], "User {$status} successfully");
        } catch (NotFoundException $e) {
            return ApiResponse(message: $e->getMessage(), code: 404);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function getLanguage(): JsonResponse
    {
        try {
            $id = getAuthUser('api_tenant')->id;
            $lang = $this->userService->getLanguage($id);
            return ApiResponse(['lang' => $lang], 'User language retrieved successfully');
        } catch (NotFoundException $e) {
            return ApiResponse(message: $e->getMessage(), code: 404);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function changeLanguage(ChangeLanguageRequest $request): JsonResponse
    {
        try {
            $id = getAuthUser('api_tenant')->id;
            $this->userService->changeLanguage($id, $request->validated()['lang']);
            return ApiResponse([], 'User language changed successfully');
        } catch (NotFoundException $e) {
            return ApiResponse(message: $e->getMessage(), code: 404);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function target($user_id): JsonResponse
    {
        try {
            $target = $this->userService->getTarget($user_id);
            return ApiResponse($target, 'User target retrieved successfully');
        } catch (NotFoundException $e) {
            return ApiResponse(message: $e->getMessage(), code: 404);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function assignToTeam(AssignToTeamRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $assignToTeamDTO = AssignToTeamDTO::fromRequest($request);
            $user = $this->userService->assignToTeam($assignToTeamDTO);
            DB::commit();
            return ApiResponse(new UserResource($user), 'User assigned to team successfully');
        } catch (NotFoundException $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: 404);
        } catch (ValidationException $e) {
            // dd($e->errors()->all());
            DB::rollBack();
            return ApiResponse(message: $e->errors(), code: 422);
        } catch (Exception $e) {
            dd($e);
            DB::rollBack();
            return ApiResponse(message: $e, code: 500);
        }
    }

    public function endAssignment($user_id): JsonResponse
    {
        try {
            $this->userService->endAssignment($user_id);
            return ApiResponse([], 'User assignment ended successfully');
        } catch (GeneralException $e) {
            return ApiResponse(message: $e->getMessage(), code: 404);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
