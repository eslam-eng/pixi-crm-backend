<?php

namespace App\Http\Controllers\Central\Api;

use App\Enums\ActivationStatus;
use App\Enums\CompanySizes;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientCollection;
use App\Http\Resources\ClientResource;
use App\Http\Resources\LandlordSubscription\InvoiceCollection;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Setting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index']);
        // $this->middleware('permission:tiers.add')->only(['store']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Invoice::query();

            // Get pagination per page from request or default to 10
            $perPage = $request->get('per_page', 10);

            // Paginate the results
            $invoices = $query->paginate($perPage);

            return ApiResponse(new InvoiceCollection($invoices), 'Invoices retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

}
