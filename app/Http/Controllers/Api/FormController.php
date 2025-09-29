<?php

namespace App\Http\Controllers\Api;

use App\DTO\Form\FormDTO;
use App\Models\Tenant\Form;
use App\Services\FormService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Form\FormRequest;
use App\Http\Resources\Tenant\FormResource;
use Exception;
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

    public function store(FormRequest $request): JsonResponse
    {
        try {
            $form = $this->formService->createForm(FormDTO::fromRequest($request));
            return ApiResponse(
                message: 'Form created successfully',
                data: new FormResource($form),
                code: Response::HTTP_CREATED
            );
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $formId): JsonResponse
    {
        try {
            $form = $this->formService->findById($formId, withRelations: ['fields' => function ($query) {
                $query->with('dependsOn')->orderBy('order');
            }]);

            return response()->json([
                'success' => true,
                'data' => new FormResource($form),
                'message' => 'Form retrieved successfully'
            ]);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(FormRequest $request, $formId): JsonResponse
    {
        try {
            $formDto = FormDTO::fromRequest($request);
            $form = $this->formService->update(formDTO: $formDto, id: $formId);

            return ApiResponse(
                message: 'Form updated successfully',
                data: new FormResource($form->load(['fields']))
            );
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $formId): JsonResponse
    {
        try {
            $this->formService->delete($formId);
            return ApiResponse(
                message: 'Form deleted successfully'
            );
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function toggle(Form $form): JsonResponse
    {
        try {
            $form->update(['is_active' => !$form->is_active]);

            return ApiResponse(
                message: 'Form status updated successfully',
                data: ['is_active' => $form->is_active]
            );
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
