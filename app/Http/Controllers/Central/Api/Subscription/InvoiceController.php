<?php

namespace App\Http\Controllers\Central\Api\Subscription;

use App\Http\Controllers\Controller;
use App\Http\Resources\Central\InvoiceResource;
use App\Services\Central\Invoice\InvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceService $invoiceService) {}

    public function index(Request $request)
    {
        $filters = $request->all();
        $limit = $request->input('limit', 15);
        $invoices = $this->invoiceService->paginate(filters: $filters, perPage: $limit);

        return InvoiceResource::collection($invoices);
    }

    public function show($invoice_id)
    {
        $invoice = $this->invoiceService->findById(id: $invoice_id, withRelation: ['items', 'tenant.owner:id,first_name,last_name,email']);

        return InvoiceResource::make($invoice);
    }
}
