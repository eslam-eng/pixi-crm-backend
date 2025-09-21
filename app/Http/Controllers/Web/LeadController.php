<?php

namespace App\Http\Controllers\Web;

use App\Models\Tenant\User;
use App\Models\Stage;
use App\Models\Reason;
use App\Models\Contact;
use App\Models\Pipline;
use App\Models\Service;
use App\Models\Industry;
use App\Models\Location;
use App\DTO\Lead\LeadDTO;
use App\Models\CustomField;
use Illuminate\Http\Request;
use App\Services\LeadService;
use App\DataTables\LeadsDataTable;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lead\StoreLeadRequest;
use App\Http\Requests\Lead\UpdateLeadRequest;

class LeadController extends Controller
{
    public function __construct(public LeadService $leadService) {
        $this->middleware('permission:view leads', ['only' => ['index', 'show']]);
        $this->middleware('permission:create leads', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit leads', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete leads', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(LeadsDataTable $dataTable, Request $request)
    {
        $filters = array_filter($request->get('filters', []), function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });
        $withRelations = ['stages','contact']; // Include the 'lead' relationship
        return $dataTable->with(['filters' => $filters, 'withRelations' => $withRelations])->render('layouts.dashboard.lead.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $industries = Industry::all();
        $contacts = Contact::all();
        $reasons = Reason::all();
        $customFields = CustomField::all();
        $stages = Stage::all();
        $pipelines = Pipline::all();
        $cities = Location::where('status', 1)->get();
        $services = Service::with('categories')->get();
        $salesUsers = User::role('sales')->get();
        return view('layouts.dashboard.lead.create', compact( 'stages','pipelines','salesUsers','industries', 'contacts', 'customFields', 'services','cities','reasons'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLeadRequest $request)
    {
        try {
            DB::beginTransaction();
            // Create leadDTO from the request
            $leadDTO = LeadDTO::fromRequest($request);
            // Store the lead using the service
            $lead = $this->leadService->store($leadDTO);
            $toast = [
                'type' => 'success',
                'title' => 'Success',
                'message' => trans('app.lead_created_successfully')
            ];
            DB::commit();
            return to_route('leads.index')->with('toast', $toast);
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $lead = $this->leadService->findById(id: $id);
        $industries = Industry::all();
        $contacts = Contact::all();
        $salesUsers = User::role('sales')->get();
        $services = Service::with('categories')->get();
        $reasons = Reason::all();
        $stages = Stage::all();
        $customFields = CustomField::all();

        return view('layouts.dashboard.lead.edit', compact(
            'lead',
            'industries',
            'contacts',
            'salesUsers',
            'services',
            'reasons',
            'stages',
            'customFields'
        ));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLeadRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            // Create leadDTO from the request
            $leadDTO = LeadDTO::fromRequest($request);
            // Update the lead using the lead
            $lead = $this->leadService->update($id, $leadDTO);
            // Success message
            $toast = [
                'type' => 'success',
                'title' => 'Success',
                'message' => trans('app.lead_updated_successfully')
            ];
            DB::commit();
            return to_route('leads.index')->with('toast', $toast);
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
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try{
            $this->leadService->delete($id);
            $toast = [
                'type' => 'success',
                'title' => 'success',
                'message' => trans('app.lead_deleted_successfully')
            ];
            return to_route('leads.index')->with('toast', $toast);
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
