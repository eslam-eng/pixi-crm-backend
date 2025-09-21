<?php

namespace App\Http\Controllers\Web;

use App\Models\Location;
use App\Models\Resource;
use Illuminate\Http\Request;
use App\DTO\Contact\ContactDTO;
use App\Services\ContactService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\DataTables\ContactsDataTable;
use App\Http\Requests\Contacts\ContactStoreRequest;
use App\Http\Requests\Contacts\ContactUpdateRequest;

class ContactController extends Controller
{
    public function __construct(public ContactService $contactService) {
        $this->middleware('permission:view contacts', ['only' => ['index', 'show']]);
        $this->middleware('permission:create contacts', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit contacts', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete contacts', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(ContactsDataTable $dataTable, Request $request)
    {
        $filters = array_filter($request->get('filters', []), function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });
        $withRelations = []; // Include the 'contact' relationship
        return $dataTable->with(['filters' => $filters, 'withRelations' => $withRelations])->render('layouts.dashboard.contact.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cities = Location::cities()->get();
        $resources = Resource::all();
        return view('layouts.dashboard.contact.create', compact('cities', 'resources'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContactStoreRequest $request)
    {
        try {
            DB::beginTransaction();
            // Create ContactDTO from the request
            $contactDTO = ContactDTO::fromRequest($request);
            // Store the contact using the service
            $contact = $this->contactService->store($contactDTO);
            $toast = [
                'type' => 'success',
                'title' => 'Success',
                'message' => trans('app.contact_created_successfully')
            ];
            DB::commit();
            return to_route('contacts.index')->with('toast', $toast);
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
    public function edit( $id)
    {
        $cities = Location::cities()->get();
        $contact = $this->contactService->findById(id: $id);
        return view('layouts.dashboard.contact.edit', compact('cities','contact'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContactUpdateRequest $request,  $id)
    {
        try {
            DB::beginTransaction();
            // Create ContactDTO from the request
            $contactDTO = ContactDTO::fromRequest($request);
            // Update the contact using the contact
            $contact = $this->contactService->update($id, $contactDTO);
            // Success message
            $toast = [
                'type' => 'success',
                'title' => 'Success',
                'message' => trans('app.contact_updated_successfully')
            ];
            DB::commit();
            return to_route('contacts.index')->with('toast', $toast);
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
            $this->contactService->delete($id);
            $toast = [
                'type' => 'success',
                'title' => 'success',
                'message' => trans('app.contact_deleted_successfully')
            ];
            return to_route('contacts.index')->with('toast', $toast);
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
