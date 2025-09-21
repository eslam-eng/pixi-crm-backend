<?php

namespace App\Http\Controllers\Web;

use App\Models\Pipline;
use Illuminate\Http\Request;
use App\DTO\Pipeline\PipelineDTO;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\DataTables\PiplinesDataTable;
use App\Http\Requests\Pipeline\PipelineStoreRequest;
use App\Http\Requests\Pipeline\PipelineUpdateRequest;
use App\Services\PipelineService;

class PiplineController extends Controller
{
    public function __construct(public PipelineService $piplineService)
    {
        $this->middleware('permission:view piplines', ['only' => ['index', 'show']]);
        $this->middleware('permission:edit piplines', ['only' => ['edit', 'update']]);
        $this->middleware('permission:create piplines', ['only' => ['create', 'store']]);
        $this->middleware('permission:delete piplines', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the pipline.
     */
    public function index(PiplinesDataTable $dataTable, Request $request)
    {
        $filters = array_filter($request->get('filters', []), function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });
        $withRelations = ['stages'];
        return $dataTable->with(['filters' => $filters, 'withRelations' => $withRelations])->render('layouts.dashboard.pipline.index');
    }

    /**
     * Show the form for creating a new pipline.
     */
    public function create()
    {
        return view('layouts.dashboard.pipline.create');
    }

    /**
     * Store a newly created pipline in storage.
     */
    public function store(PipelineStoreRequest $request)
    {
        try {
            DB::beginTransaction();
            // Create piplineDTO from the request
            $piplineDTO = PipelineDTO::fromRequest($request);
            // Store the pipline using the service
            $pipline = $this->piplineService->store($piplineDTO);
            $toast = [
                'type' => 'success',
                'title' => 'Success',
                'message' => trans('app.pipline_created_successfully')
            ];
            DB::commit();
            return to_route('piplines.index')->with('toast', $toast);
        } catch (\Exception $e) {
            DB::rollBack();
            $toast = [
                'type' => 'error',
                'title' => 'Error',
                'message' => $e->getMessage()
            ];
            return back()->with('toast', $toast);
        }
    }

    /**
     * Display the specified pipline.
     */
    public function show(Pipline $pipline)
    {
        return view('layouts.dashboard.pipline.show', compact('pipline'));
    }

    /**
     * Show the form for editing the specified pipline.
     */
    public function edit(Pipline $pipline)
    {
        return view('layouts.dashboard.pipline.edit', compact('pipline'));
    }

    /**
     * Update the specified pipline in storage.
     */
    public function update(PipelineUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Retrieve the pipline by ID
            $pipline = Pipline::findOrFail($id); // Fetch the Pipline model

            // Create piplineDTO from the request
            $piplineDTO = PipelineDTO::fromRequest($request);

            // Update the pipline using the pipline model
            $pipline = $this->piplineService->update($pipline, $piplineDTO); // Pass the model instead of the ID

            // Success message
            $toast = [
                'type' => 'success',
                'title' => 'Success',
                'message' => trans('app.pipline_updated_successfully')
            ];

            DB::commit();
            return to_route('piplines.index')->with('toast', $toast);
        } catch (\Exception $e) {
            DB::rollBack();
            // Error message
            $toast = [
                'type' => 'error',
                'title' => 'Error',
                'message' => $e->getMessage()
            ];
            return back()->with('toast', $toast);
        }
    }



    /**
     * Remove the specified pipline from storage.
     */
    public function destroy(int $id)
    {
        try {
            $this->piplineService->delete($id);
            $toast = [
                'type' => 'success',
                'title' => 'success',
                'message' => trans('app.pipline_deleted_successfully')
            ];
            return to_route('piplines.index')->with('toast', $toast);
        } catch (\Exception $e) {
            $toast = [
                'type' => 'error',
                'title' => 'error',
                'message' => trans('app.there_is_an_error')
            ];
            return back()->with('toast', $toast);
        }
    }
}
