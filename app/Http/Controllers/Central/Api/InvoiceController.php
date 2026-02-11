<?php

namespace App\Http\Controllers\Central\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Central\InvoiceResource;
use App\Http\Resources\Central\InvoiceShowResource;
use App\Services\Central\Invoice\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceService $invoiceService)
    {
    }

    /**
     * Display a listing of invoices.
     */
    public function index(Request $request): JsonResponse
    {
        $invoices = $this->invoiceService->getQuery(
            filters: $request->all(),
            withRelation: ['tenant', 'subscription']
        )->latest()->paginate($request->get('per_page', 15));

        return apiResponse(
            message: 'Invoices retrieved successfully',
            data: InvoiceResource::collection($invoices)->response()->getData(true)
        );
    }

    /**
     * Display subscription statistics.
     */
    public function statics(): JsonResponse
    {
        $statics = $this->invoiceService->statics();

        return apiResponse(
            data: $statics,
            message: 'Invoice statistics retrieved successfully'
        );
    }

    /**
     * Display the specified invoice.
     */
    public function show(string $id): JsonResponse
    {
        $invoice = $this->invoiceService->findById($id, ['tenant.owner', 'subscription', 'items']);

        return apiResponse(
            message: 'Invoice retrieved successfully',
            data: new InvoiceShowResource($invoice)
        );
    }
}
