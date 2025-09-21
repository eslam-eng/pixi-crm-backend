<?php

namespace App\Http\Controllers\Web;

use Exception;
use Illuminate\Http\Request;
use App\DTO\Resource\ResourceDTO;
use App\Services\ResourceService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\DataTables\ResourcesDataTable;
use SebastianBergmann\CodeUnit\NoTraitException;
use App\Http\Requests\Resource\ResourceStoreRequest;
use App\Http\Requests\Resource\ResourceUpdateRequest;

class ResourceController extends Controller
{
    public function __construct(public ResourceService $resourceService){
        $this->middleware('permission:view sources', ['only' => ['index','show']]);
        // $this->middleware('permission:show sources', ['only' => ['show']]);
        $this->middleware('permission:edit sources', ['only' => ['edit', 'update']]);
        $this->middleware('permission:create sources', ['only' => ['create', 'store']]);
        $this->middleware('permission:delete sources', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(ResourcesDataTable $dataTable,Request $request)
    {
        $filters = array_filter($request->get('filters', []), function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });
        $withRelations = [];
        return $dataTable->with(['filters' => $filters, 'withRelations' => $withRelations])->render('layouts.dashboard.resource.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try{
            $withRelations=[];
            $resource=$this->resourceService->findById(id: $id, withRelations:$withRelations);
            return view('layouts.dashboard.resource.show',['resource'=>$resource]);
        }catch(NoTraitException $e){
            $toast = [
                'type' => 'error',
                'title' => 'Error',
                'message' => $e->getMessage()
            ];
            return to_route('resources.index')->with('toast', $toast);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('layouts.dashboard.resource.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ResourceStoreRequest $request)
    {
        try{
            DB::beginTransaction();
            $resourceDTO=ResourceDTO::fromRequest($request);
            $resource=$this->resourceService->store($resourceDTO);
            $toast = [
                'type' => 'success',
                'title' => 'success',
                'message' => trans('app.resource_created_successfully')
            ];
            DB::commit();
            return to_route('resources.index')->with('toast', $toast);

        } catch (\Exception $e) {
            DB::rollBack();
            $toast = [
                'type' => 'error',
                'title' => 'error',
                'message' => $e->getMessage()
            ];
            DB::commit();
            return back()->with('toast', $toast);
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit( $id)
    {
        try{
            $resource=$this->resourceService->findById(id:$id);
            return view('layouts.dashboard.resource.edit',compact('resource'));
        }catch(Exception $e){
            return redirect()->back();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ResourceUpdateRequest $request,  $id)
    {
        try{
            $resourceDTO=$request->toResourceDTO();
            $this->resourceService->update($resourceDTO,$id);
            $toast = [
                'type' => 'success',
                'title' => 'success',
                'message' => trans('app.resource_updated_successfully')
            ];
            return to_route('resources.index')->with('toast', $toast);
        } catch (\Exception $e) {
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
     */
    public function destroy(int $id)
    {
        try {
            $this->resourceService->delete($id);
            return apiResponse(message: 'deleted successfully');
        } catch (\Exception $exception) {
            return apiResponse(message: $exception->getMessage(), code: 500);
        }
    }
}
