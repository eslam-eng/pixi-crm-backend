<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\DataTables\LocationsDataTable;
use App\DTO\Location\LocationDTO;
use Illuminate\Support\Facades\DB;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Locations\SubLocationStoreRequest;
use App\Http\Requests\Locations\LocationUpdateRequest;
use App\Http\Requests\Locations\LocationStoreRequest;

use App\Services\LocationService;
use Illuminate\Database\QueryException;

class LocationController extends Controller
{
    public function __construct(public LocationService $locationService) {
        $this->middleware('permission:view locations', ['only' => ['index','show']]);
        // $this->middleware('permission:show locations', ['only' => ['show']]);
        $this->middleware('permission:edit locations', ['only' => ['edit', 'update']]);
        $this->middleware('permission:create locations', ['only' => ['create', 'store']]);
        $this->middleware('permission:delete locations', ['only' => ['destroy']]);
        $this->middleware('permission:create areas', ['only' => ['createArea']]);
        $this->middleware('permission:store areas', ['only' => ['storeArea']]);
    }

    public function index(LocationsDataTable $dataTable, Request $request)
    {
        $filters = array_filter($request->get('filters', []), function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });
        $withRelations = [];
        return $dataTable->with(['filters' => $filters, 'withRelations' => $withRelations])->render('layouts.dashboard.location.index');
    }

    public function create()
    {
        $blade = $this->locationService->create();
        return view($blade);
    }

    public function show(int $id)
    {
        try {
            $withRelations = [];
            $location = $this->locationService->findById(id: $id, withRelations: $withRelations);
            return view('layouts.dashboard.location.show', ['location' => $location]);
        } catch (NotFoundException $exception) {
            $toast = [
                'type' => 'error',
                'title' => 'Error',
                'message' => $exception->getMessage()
            ];
            return to_route('locations.index')->with('toast', $toast);
        }
    }

    public function store(LocationStoreRequest $request)
    {
        try {
            DB::beginTransaction();
            $locationDTO = LocationDTO::fromRequest($request);
            $this->locationService->store($locationDTO);
            $toast = [
                'type' => 'success',
                'title' => 'success',
                'message' => trans('app.location_created_successfully')
            ];
            DB::commit();
            return to_route('locations.index')->with('toast', $toast);
        } catch (\Exception $exception) {
            DB::rollBack();
            $toast = [
                'type' => 'error',
                'title' => 'error',
                'message' => trans('app.there_is_an_error')
            ];
            DB::commit();
            return back()->with('toast', $toast);
        }
    }

    public function edit($id)
    {
        try {
            $blade = $this->locationService->edit($id);
            $location = $this->locationService->findById(id: $id);
            $subLocation = $this->locationService->getAll(filters: ['parent' => $location->id]);
            return view($blade, get_defined_vars());
        } catch (\Exception $e) {
            return redirect()->back();
        }
    }

    public function update(LocationUpdateRequest $request, $id)
    {
        try {
            $locationDTO = $request->tolocationDTO();
            $this->locationService->update($locationDTO, $id);
            $toast = [
                'type' => 'success',
                'title' => 'success',
                'message' => trans('app.location_updated_successfully')
            ];
            return to_route('locations.index')->with('toast', $toast);
        } catch (\Exception $exception) {
            dd($exception);
            $toast = [
                'type' => 'error',
                'title' => 'error',
                'message' => trans('app.there_is_an_error')
            ];
            return back()->with('toast', $toast);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->locationService->delete($id);
            return apiResponse(message: 'deleted successfully');
        } catch (QueryException $e) {
            // Check if the error is due to foreign key constraint
            if ($e->getCode() == 23000) {
                return apiResponse(message: 'Cannot delete this location because it has child locations.', code: 400);
            }
        } catch (\Exception $exception) {
            return apiResponse(message: $exception->getMessage(), code: 500);
        }
    }

    public function storeSubLocation(SubLocationStoreRequest $request)
    {
        try {
            $locationDTO = LocationDTO::fromRequest($request);
            $this->locationService->storeSubLocation($locationDTO);
            $toast = [
                'type' => 'success',
                'title' => 'success',
                'message' => trans('app.location_created_successfully')
            ];
            return to_route('locations.index')->with('toast', $toast);
        } catch (\Exception $exception) {
            $toast = [
                'type' => 'error',
                'title' => 'error',
                'message' => trans('app.there_is_an_error')
            ];
            return back()->with('toast', $toast);
        }
    }

    public function UpdateSubLocation(SubLocationStoreRequest $request, $id)
    {
        try {
            $locationDTO = LocationDTO::fromRequest($request);
            $this->locationService->UpdateSubLocation($locationDTO,locationId: $id);
            $toast = [
                'type' => 'success',
                'title' => 'success',
                'message' => trans('app.location_created_successfully')
            ];
            return to_route('locations.index')->with('toast', $toast);
        } catch (\Exception $exception) {
            $toast = [
                'type' => 'error',
                'title' => 'error',
                'message' => trans('app.there_is_an_error')
            ];
            return back()->with('toast', $toast);
        }
    }
}
