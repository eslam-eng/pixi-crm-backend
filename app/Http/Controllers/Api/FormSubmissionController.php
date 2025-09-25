<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Form;
use App\Services\FormSubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;


class FormSubmissionController extends Controller
{
    public function __construct(
        private FormSubmissionService $formSubmissionService
    ) {}

    public function submit(Request $request, string $slug): JsonResponse
    {
        $form = $this->formSubmissionService->getFormBySlug($slug);

        if (!$form) {
            ApiResponse(
                message: 'Form not found or inactive',
                code: Response::HTTP_NOT_FOUND
            );
        }

        // Build dynamic validation rules
        $rules = $this->formSubmissionService->buildValidationRules($form);
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $submission = $this->formSubmissionService->submitForm(
                $form,
                $request->only($form->fields->pluck('name')->toArray()),
                $request->ip(),
                $request->userAgent()
            );

            $response = [
                'message' => 'Form submitted successfully',
                'data' => [
                    'submission_id' => $submission->id,
                    'form_id' => $form->id,
                    'submitted_at' => $submission->created_at
                ]
            ];

            // Add redirect URL if exists
            $redirectUrl = $this->formSubmissionService->getRedirectUrl($form);
            if ($redirectUrl) {
                $response['data']['redirect_url'] = $redirectUrl;
            }

            return response()->json($response, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to submit form',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function submissions(Form $form): JsonResponse
    {
        $submissions = $form->submissions()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'message' => 'Submissions retrieved successfully',
            'data' => $submissions->items(),
            'meta' => [
                'current_page' => $submissions->currentPage(),
                'total' => $submissions->total(),
                'per_page' => $submissions->perPage(),
            ]
        ]);
    }


}
