<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\DTO\Reason\ReasonDTO;
use App\Services\ReasonService;
use Illuminate\Support\Facades\DB;
use App\DataTables\ReasonsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reason\ReasonStoreRequest;
use App\Http\Requests\Reason\ReasonUpdateRequest;
use Exception;
use SebastianBergmann\CodeUnit\NoTraitException;

class ReasonController extends Controller
{
    public function __construct(public ReasonService $reasonService) {
        $this->middleware('permission:view reasons', ['only' => ['index','show']]);
        // $this->middleware('permission:show reasons', ['only' => ['show']]);
        $this->middleware('permission:edit reasons', ['only' => ['edit', 'update']]);
        $this->middleware('permission:create reasons', ['only' => ['create', 'store']]);
        $this->middleware('permission:delete reasons', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(ReasonsDataTable $dataTable,Request $request)
    {
        $filters = array_filter($request->get('filters', []), function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });
        $withRelations = [];
        return $dataTable->with(['filters' => $filters, 'withRelations' => $withRelations])->render('layouts.dashboard.reason.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('layouts.dashboard.reason.create');
    }

    public function store(ReasonStoreRequest $request)
    {
        try{
            DB::beginTransaction();
            $reasonDTO=ReasonDTO::fromRequest($request);
            $reason=$this->reasonService->store($reasonDTO);
            $toast = [
                'type' => 'success',
                'title' => 'success',
                'message' => trans('app.reason_created_successfully')
            ];
            DB::commit();
            return to_route('reasons.index')->with('toast', $toast);

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
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try{
            $withRelations=[];
            $reason=$this->reasonService->findById(id: $id, withRelations:$withRelations);
            return view('layouts.dashboard.reason.show',['reason'=>$reason]);
        }catch(NoTraitException $e){
            $toast = [
                'type' => 'error',
                'title' => 'Error',
                'message' => $e->getMessage()
            ];
            return to_route('reasons.index')->with('toast', $toast);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( $id)
    {
        try{
            $reason=$this->reasonService->findById(id:$id);
            return view('layouts.dashboard.reason.edit',compact('reason'));
        }catch(Exception $e){
            return redirect()->back();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReasonUpdateRequest $request,  $id)
    {
        try{
            $reasonDTO=$request->toReasonDTO();
            $this->reasonService->update($reasonDTO,$id);
            $toast = [
                'type' => 'success',
                'title' => 'success',
                'message' => trans('app.reason_updated_successfully')
            ];
            return to_route('reasons.index')->with('toast', $toast);
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
            $this->reasonService->delete($id);
            return apiResponse(message: 'deleted successfully');
        } catch (\Exception $exception) {
            return apiResponse(message: $exception->getMessage(), code: 500);
        }
    }
}
