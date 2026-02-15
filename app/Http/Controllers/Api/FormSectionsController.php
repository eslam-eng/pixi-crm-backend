<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FormSectionResource;
use App\Models\Tenant\FormSection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FormSectionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage-settings')->only(['store', 'update', 'destroy']);
        // Index might need permission too, but listing for usage usually is less restricted?
        // User didn't specify, I will stick to what CustomFieldsController has or generic.
        // CustomFieldsController has 'manage-settings'.
        // I'll add it for now, or maybe not if it's for form rendering.
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $query = FormSection::query();

            if ($request->has('module')) {
                $query->byModule($request->input('module'));
            }

            $sections = $query->orderBy('ordering')->get();

            return ApiResponse(FormSectionResource::collection($sections), 'Form sections retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
