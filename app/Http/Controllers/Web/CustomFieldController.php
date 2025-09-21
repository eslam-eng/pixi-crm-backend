<?php

namespace App\Http\Controllers\Web;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\CustomFieldService;
use App\Exceptions\NotFoundException;
use App\DTO\CustomField\CustomFieldDTO;
use App\DataTables\CustomFieldsDataTable;
use App\Http\Requests\CustomFields\StoreCustomFieldRequest;
use App\Http\Requests\CustomFields\UpdateCustomFieldRequest;

class CustomFieldController extends Controller
{
    public function __construct(public CustomFieldService $customFieldService){
        $this->middleware('permission:view customFields', ['only' => ['index','show']]);
        // $this->middleware('permission:show customFields', ['only' => ['show']]);
        $this->middleware('permission:edit customFields', ['only' => ['edit', 'update']]);
        $this->middleware('permission:create customFields', ['only' => ['create', 'store']]);
        $this->middleware('permission:delete customFields', ['only' => ['destroy']]);

    }
    /**
     * Display a listing of the resource.
     */
    public function index(CustomFieldsDataTable $dataTable,Request $request)
    {
        $filters = array_filter($request->get('filters', []), function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });
        $withRelations = [];
        return $dataTable->with([
            'filters' => $filters,
            'withRelations' => $withRelations
        ])->render('layouts.dashboard.customField.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('layouts.dashboard.customField.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomFieldRequest $request)
    {
        try{
            DB::beginTransaction();
            $customFieldDTO=CustomFieldDTO::fromRequest($request);
            $customField=$this->customFieldService->store($customFieldDTO);
            DB::commit();
            return to_route('custom-fields.index')->with('toast', [
                'type' => 'success',
                'title' => 'Success',
                'message' => trans('app.customField_created_successfully')
            ]);

        }catch (\Exception $e) {
            DB::rollBack();
            return back()->with('toast', [
                'type' => 'error',
                'title' => 'Error',
                'message' => trans('app.there_is_an_error')
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try {
            $withRelations = [];
            $customField=$this->customFieldService->findById(id:$id, withRelations: $withRelations);
            return view('layouts.dashboard.customField.show',['customField'=>$customField]);
        }catch(NotFoundException $e){
            $toast = [
                'type' => 'error',
                'title' => 'Error',
                'message' => $e->getMessage()
            ];
            return to_route('custom-fields.index')->with('toast', $toast);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {try{
        $customField=$this->customFieldService->findById(id:$id);
        return view('layouts.dashboard.customField.edit',compact('customField'));
    }catch(Exception $e){
        return redirect()->back();
    }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomFieldRequest $request,  $id)
    {
        try{
            $customFieldDTO=$request->toCustomFieldDTO();
            $this->customFieldService->update($customFieldDTO,$id);
            $toast = [
                'type' => 'success',
                'title' => 'success',
                'message' => trans('app.customField_updated_successfully')
            ];
            return to_route('custom-fields.index')->with('toast', $toast);
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
            $this->customFieldService->delete($id);
            return apiResponse(message: 'deleted successfully');
        } catch (\Exception $exception) {
            return apiResponse(message: $exception->getMessage(), code: 500);
        }
    }
}
