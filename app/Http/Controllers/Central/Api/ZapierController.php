<?php

namespace App\Http\Controllers\Central\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\ContactService;
use App\DTO\Contact\ContactDTO;
use App\Models\Tenant\Contact;
use Carbon\Carbon;

class ZapierController extends Controller
{
    public function __construct(protected ContactService $contactService)
    {
    }

    /**
     * Return tenant details for Zapier authentication test.
     */
    public function me(Request $request)
    {
        $tenant = tenant();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $tenant->id,
                'name' => $tenant->name ?? 'Tenant',
                'domain' => $tenant->domains->first()?->domain,
                'logged_in_as' => auth()->user()?->email ?? 'System',
            ]
        ]);
    }

    /**
     * Zapier Trigger: New Contact
     * Polling endpoint to get recent contacts.
     */
    public function getContacts(Request $request)
    {
        $query = Contact::query();

        if ($request->has('created_after')) {
            $query->where('created_at', '>', Carbon::parse($request->created_after));
        }

        $contacts = $query->orderBy('created_at', 'asc')
            ->limit(50)
            ->get();

        // Return flat array as expected by Zapier
        return response()->json($contacts);
    }

    /**
     * Zapier Action: Create Contact
     */
    public function createContact(Request $request)
    {
        try {
            DB::beginTransaction();

            $contactDTO = ContactDTO::fromRequest($request);
            $contact = $this->contactService->store($contactDTO);

            DB::commit();

            return apiResponse($contact, 'Contact created successfully', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
