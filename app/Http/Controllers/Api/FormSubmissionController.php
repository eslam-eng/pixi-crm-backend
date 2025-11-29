<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Tenant\FormSubmissionResource;
use App\Services\FormSubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FormSubmissionController extends Controller
{
    public function __construct(
        private FormSubmissionService $formSubmissionService
    ) {}

    public function submit(Request $request, string $slug): JsonResponse
    {
        $form = $this->formSubmissionService->getFormBySlug($slug);

        try {
            // Get current form data for dynamic validation
            $formData = $request->all();
            $validationRules = $form->getDynamicValidationRules($formData);
            // Validate with dynamic rules
            $validated = $request->validate($validationRules);

            // Create submission
            $submission = $form->submissions()->create([
                'data' => $validated,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $form->increment('submissions_count');

            return ApiResponse(
                message: 'Form submitted successfully',
                data: FormSubmissionResource::make($submission),
                code: Response::HTTP_CREATED
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse(message: 'Validation failed', data: $e->errors(), code: Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function submissions(int $formId): JsonResponse
    {
        try {
            $form = $this->formSubmissionService->formService->findById($formId);
            $submissions = $form->submissions()
                ->orderBy('created_at', 'desc')
                ->paginate(15);
            $data = FormSubmissionResource::collection($submissions)->response()->getData(true);
            return ApiResponse(message: 'Submissions retrieved successfully', data: $data, code: Response::HTTP_OK);
        } catch (\Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
