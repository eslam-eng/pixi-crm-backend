<?php

namespace App\Http\Controllers\Api;

use App\DTO\Form\FormDTO;
use App\Models\Tenant\Form;
use App\Services\FormService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Form\StoreFormRequest;
use App\Http\Requests\Form\UpdateFormRequest;
use App\Http\Resources\Tenant\FormResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class FormController extends Controller
{
    public function __construct(
        private FormService $formService
    ) {}

    public function index(): JsonResponse
    {
        $forms = $this->formService->index();
        $data = FormResource::collection($forms)->response()->getData(true);
        return ApiResponse(message: 'Forms retrieved successfully', data: $data);
    }

    public function store(StoreFormRequest $request): JsonResponse
    {
        $form = $this->formService->createForm(FormDTO::fromRequest($request));

        return ApiResponse(
            message: 'Form created successfully',
            data: new FormResource($form),
            code: Response::HTTP_CREATED
        );
    }

    public function show(Form $form): JsonResponse
    {
        return response()->json([
            'message' => 'Form retrieved successfully',
            'data' => new FormResource($form->load(['fields']))
        ]);
    }

    public function update(UpdateFormRequest $request, Form $form): JsonResponse
    {
        $form->update($request->validated());

        return ApiResponse(
            message: 'Form updated successfully',
            data: new FormResource($form->load(['fields']))
        );
    }

    public function destroy(Form $form): JsonResponse
    {
        $form->delete();
        return ApiResponse(
            message: 'Form deleted successfully'
        );
    }

    public function toggle(Form $form): JsonResponse
    {
        $form->update(['is_active' => !$form->is_active]);

        return ApiResponse(
            message: 'Form status updated successfully',
            data: ['is_active' => $form->is_active]
        );
    }
}
