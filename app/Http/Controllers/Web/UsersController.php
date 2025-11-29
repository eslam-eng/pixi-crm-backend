<?php

namespace App\Http\Controllers\Web;

use Exception;
use Illuminate\Http\Request;

use App\DataTables\UsersDatatable;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Users\UserUpdateRequest;
use App\Http\Requests\Central\Users\UserStoreRequest;
use App\Http\Requests\Users\UserUpdateProfileRequest;
use App\Services\Central\UserService;

class UsersController extends Controller
{

    public function __construct(private UserService $userService)
    {

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UsersDatatable $usersDatatable, Request $request)
    {
        try {
            $user = getAuthUser();
            $filters = array_filter($request->get('filters', []), function ($value) {
                return ($value !== null && $value !== false && $value !== '');
            });
            return $usersDatatable->with(['filters' => $filters])->render('layouts.dashboard.users.index');
        } catch (Exception $e) {
            $toast = [
                'type' => 'error',
                'title' => 'error',
                'message' => $e->getMessage()
            ];
            return back()->with('toast', $toast);
        }
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        try {
            return view('layouts.dashboard.users.create');
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
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserStoreRequest $request)
    {
        try {
            $this->userService->store($request->toUserDTO());
            $toast = [
                'type' => 'success',
                'title' => 'success',
                'message' => trans('app.user_created_successfully')
            ];
            return redirect()->route('users.index')->with('toast', $toast);
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
    public function show($id)
    {
        try {
            $user = $this->userService->findById(id: $id);
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
            $user = $this->userService->findById(id: $id);
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
            $this->userService->update($userDTO, $id);
            $toast = [
                'type' => 'success',
                'title' => 'success',
                'message' => trans('app.user_updated_successfully')
            ];
            return to_route('users.index')->with('toast', $toast);
        } catch (Exception $e) {
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

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $this->userService->destroy(id: $id);
            return apiResponse(message: trans('lang.success_operation'));
        } catch (NotFoundException $e) {
            return apiResponse(message: $e->getMessage(), code: 422);
        } catch (Exception $e) {
            return apiResponse(message: trans('lang.something_went_wrong'), code: 422);
        }
    }
}
