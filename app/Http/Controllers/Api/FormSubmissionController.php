<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Form;
use App\Services\FormService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class FormSubmissionController extends Controller
{
    public function __construct(
        private FormService $formService
    ) {}

    public function submit(Request $request, string $slug): JsonResponse
    {
        $form = Form::with(['fields', 'actions'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$form) {
            return response()->json([
                'message' => 'Form not found or inactive'
            ], 404);
        }

        // Build dynamic validation rules
        $rules = $this->buildValidationRules($form);
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $submission = $this->formService->submitForm(
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
            $redirectUrl = $this->formService->getRedirectUrl($form);
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

    private function buildValidationRules(Form $form): array
    {
        $rules = [];

        foreach ($form->fields as $field) {
            $fieldRules = [];

            if ($field->required) {
                $fieldRules[] = 'required';
            }

            switch ($field->type) {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'file':
                    $fieldRules[] = 'file';
                    $fieldRules[] = 'max:2048';
                    break;
            }

            if (!empty($fieldRules)) {
                $rules[$field->name] = $fieldRules;
            }
        }

        return $rules;
    }
}
