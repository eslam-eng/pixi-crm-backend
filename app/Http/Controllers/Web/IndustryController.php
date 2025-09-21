<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\DataTables\IndustriesDataTable;
use App\DTO\Industry\IndustryDTO;
use Illuminate\Support\Facades\DB;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Industries\IndustryUpdateRequest;
use App\Http\Requests\Industries\IndustryStoreRequest;

use App\Services\IndustryService;

class IndustryController extends Controller
{
    public function __construct(public IndustryService $industryService)
    {
        $this->middleware('permission:view industries', ['only' => ['index', 'show']]);
        // $this->middleware('permission:show industries', ['only' => ['show']]);
        $this->middleware('permission:edit industries', ['only' => ['edit', 'update']]);
        $this->middleware('permission:create industries', ['only' => ['create', 'store']]);
        $this->middleware('permission:delete industries', ['only' => ['destroy']]);
    }

    public function index(IndustriesDataTable $dataTable, Request $request)
    {
        $filters = array_filter($request->get('filters', []), function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });
        $withRelations = [];
        return $dataTable->with(['filters' => $filters, 'withRelations' => $withRelations])->render('layouts.dashboard.industry.index');
    }

    public function create()
    {
        return view('layouts.dashboard.industry.create');
    }

    public function show(int $id)
    {
        try {
            $withRelations = [];
            $industry = $this->industryService->findById(id: $id, withRelations: $withRelations);
            return view('layouts.dashboard.industry.show', ['industry' => $industry]);
        } catch (NotFoundException $exception) {
            $toast = [
                'type' => 'error',
                'title' => 'Error',
                'message' => $exception->getMessage()
            ];
            return to_route('industries.index')->with('toast', $toast);
        }
    }

    public function store(IndustryStoreRequest $request)
    {
        try {
            DB::beginTransaction();
            $industryDTO = IndustryDTO::fromRequest($request);
            $industry = $this->industryService->store($industryDTO);
            $toast = [
                'type' => 'success',
                'title' => 'success',
                'message' => trans('app.industry_created_successfully')
            ];
            DB::commit();
            return to_route('industries.index')->with('toast', $toast);
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
            $industry = $this->industryService->findById(id: $id);
            return view('layouts.dashboard.industry.edit', compact('industry'));
        } catch (\Exception $e) {
            return redirect()->back();
        }
    }

    public function update(IndustryUpdateRequest $request, $id)
    {
        try {
            $industryDTO = $request->toindustryDTO();
            $this->industryService->update($industryDTO, $id);
            $toast = [
                'type' => 'success',
                'title' => 'success',
                'message' => trans('app.industry_updated_successfully')
            ];
            return to_route('industries.index')->with('toast', $toast);
        } catch (\Exception $e) {
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
            $this->industryService->delete($id);
            return apiResponse(message: 'deleted successfully');
        } catch (\Exception $exception) {
            return apiResponse(message: $exception->getMessage(), code: 500);
        }
    }
}
