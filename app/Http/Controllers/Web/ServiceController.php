<?php

namespace App\Http\Controllers\Web;

use Exception;
use App\Models\Service;
use Illuminate\Http\Request;
use App\DTO\Service\ServiceDTO;
use App\Services\ServiceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\DataTables\ServicesDataTable;
use App\Exceptions\NotFoundException;
use App\Http\Requests\Service\ServiceStoreRequest;
use App\Http\Requests\Service\ServiceUpdateRequest;

class ServiceController extends Controller
{
    public function __construct(public ServiceService $serviceService){
        $this->middleware('permission:view services', ['only' => ['index','show']]);
        // $this->middleware('permission:show services', ['only' => ['show']]);
        $this->middleware('permission:edit services', ['only' => ['edit', 'update']]);
        $this->middleware('permission:create services', ['only' => ['create', 'store']]);
        $this->middleware('permission:delete services', ['only' => ['destroy']]);

    }
    /**
     * Display a listing of the resource.
     */
    public function index(ServicesDataTable $dataTable, Request $request)
    {
        $filters = array_filter($request->get('filters', []), function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });
        $withRelations = [];
        return $dataTable->with([
            'filters' => $filters,
            'withRelations' => $withRelations
        ])->render('layouts.dashboard.service.index');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $services = Service::all(); // Fetch all services
        return view('layouts.dashboard.service.create', compact('services'));

    }

    public function store(ServiceStoreRequest $request)
{
    try {
        DB::beginTransaction();

        // Check if it is a sub-service
        if ($request->has('is_sub_service') && $request->is_sub_service) {
            // Validate parent service ID
            if (empty($request->parent_service_id)) {
                throw new \Exception(trans('app.parent_service_required'));
            }

            // Save as a category for the parent service
            $parentService = Service::findOrFail($request->parent_service_id);
            $parentService->categories()->create([
                'name' => $request->name,
                'price' => $request->price,
            ]);
        } else {
            // Save as a new service
            $serviceDTO = ServiceDTO::fromRequest($request);
            $service = $this->serviceService->store($serviceDTO);
        }

        DB::commit();

        return to_route('services.index')->with('toast', [
            'type' => 'success',
            'title' => 'Success',
            'message' => trans('app.service_created_successfully'),
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('toast', [
            'type' => 'error',
            'title' => 'Error',
            'message' => $e->getMessage() ?: trans('app.there_is_an_error'),
        ]);
    }
}


    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try {
            $withRelations = ['categories'];
            $service=$this->serviceService->findById(id:$id, withRelations: $withRelations);
            return view('layouts.dashboard.service.show',['service'=>$service]);
        }catch(NotFoundException $e){
            $toast = [
                'type' => 'error',
                'title' => 'Error',
                'message' => $e->getMessage()
            ];
            return to_route('services.index')->with('toast', $toast);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        try {
            $withRelations = ['categories'];
            $service = $this->serviceService->findById(id: $id, withRelations: $withRelations);
            return view('layouts.dashboard.service.edit', compact('service'));
        } catch (Exception $e) {
            return redirect()->back();
        }
    }



    public function update(ServiceUpdateRequest $request, $id)
    {
        try{
            $serviceDTO=$request->toServiceDTO();
            $this->serviceService->update($serviceDTO,$id);
            $toast = [
                'type' => 'success',
                'title' => 'success',
                'message' => trans('app.service_updated_successfully')
            ];
            return to_route('services.index')->with('toast', $toast);
        }
        catch (\Exception $e) {
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
        try{
            $this->serviceService->delete($id);
            $toast = [
                'type' => 'success',
                'title' => 'success',
                'message' => trans('app.service_deleted_successfully')
            ];
            return to_route('services.index')->with('toast', $toast);
        }catch (\Exception $e) {
            $toast = [
                'type' => 'error',
                'title' => 'error',
                'message' => trans('app.there_is_an_error')
            ];
            return back()->with('toast', $toast);
        }
    }
}
