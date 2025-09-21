<?php

namespace App\Http\Controllers\Api;

use App\Models\Tenant\Form;
use App\Services\FormService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Http\Requests\Form\StoreFormRequest;
use App\Http\Requests\Form\UpdateFormRequest;
use App\Http\Resources\Tenant\FormResource;
use Illuminate\Http\JsonResponse;

class FormController extends Controller
{
    public function __construct(
        private FormService $formService
    ) {}

    public function index(): JsonResponse
    {
        $forms = Form::with(['fields', 'actions'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'message' => 'Forms retrieved successfully',
            'data' => FormResource::collection($forms),
            'meta' => [
                'current_page' => $forms->currentPage(),
                'total' => $forms->total(),
                'per_page' => $forms->perPage(),
            ]
        ]);
    }

    public function store(StoreFormRequest $request): JsonResponse
    {
        $form = $this->formService->createForm($request->validated());

        return response()->json([
            'message' => 'Form created successfully',
            'data' => new FormResource($form->load(['fields', 'actions']))
        ], 201);
    }

    public function show(Form $form): JsonResponse
    {
        return response()->json([
            'message' => 'Form retrieved successfully',
            'data' => new FormResource($form->load(['fields', 'actions']))
        ]);
    }

    public function update(UpdateFormRequest $request, Form $form): JsonResponse
    {
        $form->update($request->validated());

        return response()->json([
            'message' => 'Form updated successfully',
            'data' => new FormResource($form->load(['fields', 'actions']))
        ]);
    }

    public function destroy(Form $form): JsonResponse
    {
        $form->delete();

        return response()->json([
            'message' => 'Form deleted successfully'
        ]);
    }

    public function toggle(Form $form): JsonResponse
    {
        $form->update(['is_active' => !$form->is_active]);

        return response()->json([
            'message' => 'Form status updated successfully',
            'data' => ['is_active' => $form->is_active]
        ]);
    }
}
