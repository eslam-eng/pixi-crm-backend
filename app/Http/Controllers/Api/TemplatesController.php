<?php

namespace App\Http\Controllers\Api;

use App\DTO\Tenant\Template\TemplateDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Template\SendTemplateRequest;
use App\Http\Requests\Tenant\Template\TemplateRequest;
use App\Http\Resources\Tenant\Template\TemplateResource;
use App\Services\Tenant\TemplateService;
use App\Services\Tenant\WhatsAppService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TemplatesController extends Controller
{
    public function __construct(
        private readonly TemplateService $templateService,
        private readonly WhatsAppService $whatsAppService
    ) {
        $this->middleware('permission:manage-settings')->except(['index', 'show']);
    }

    public function index(): JsonResponse
    {
        try {
            $filters = array_filter(request()->all(), function ($value) {
                return $value !== null && $value !== '';
            });

            $templates = $this->templateService->index(filters: $filters, perPage: $filters['per_page'] ?? 10);
            return ApiResponse(
                TemplateResource::collection($templates)->response()->getData(true),
                'Templates retrieved successfully'
            );
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $template = $this->templateService->findById($id);
            return ApiResponse(
                new TemplateResource($template),
                'Template retrieved successfully'
            );
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function store(TemplateRequest $request): JsonResponse
    {
        try {
            $templateDTO = TemplateDTO::fromArray($request->validatedData());
            $template = $this->templateService->store($templateDTO);
            return ApiResponse(
                new TemplateResource($template),
                'Template created successfully',
                code: 201
            );
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function update(TemplateRequest $request, int $id): JsonResponse
    {
        try {
            $templateDTO = TemplateDTO::fromArray($request->validatedData());
            $this->templateService->update($templateDTO, $id);
            $template = $this->templateService->findById($id);
            return ApiResponse(
                new TemplateResource($template),
                'Template updated successfully'
            );
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->templateService->destroy($id);
            return ApiResponse(message: 'Template deleted successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function send(SendTemplateRequest $request): JsonResponse
    {
        try {
            $data = $request->validatedData();

            // Determine template type from request or find by slug
            $templateType = $data['type'] ?? 'email';
            $template = $this->templateService->findBySlug($data['template_slug'], $templateType);

            if (!$template) {
                return ApiResponse(message: 'Template not found', code: 404);
            }

            $results = [];

            foreach ($data['recipients'] as $recipient) {
                try {
                    if ($template->type === 'email' && isset($recipient['email'])) {
                        $renderedBody = $this->templateService->render($template, $recipient['email'], $data['variables'] ?? []);
                        // Render subject with variables
                        $allVariables = array_merge($data['variables'] ?? [], ['recipient_name' => $recipient['name'] ?? '']);
                        $renderedSubject = $template->subject
                            ? $this->templateService->renderSubject($template->subject, $recipient['email'], $allVariables)
                            : 'No Subject';

                        Mail::to($recipient['email'])->send(
                            new \App\Mail\TemplateMail(
                                emailSubject: $renderedSubject,
                                body: $renderedBody,
                                recipientName: $recipient['name'] ?? null
                            )
                        );

                        $results[] = [
                            'recipient' => $recipient['email'],
                            'status' => 'sent',
                            'type' => 'email'
                        ];
                    } elseif ($template->type === 'whatsapp' && isset($recipient['phone'])) {
                        $renderedBody = $this->templateService->render($template, $recipient['phone'], $data['variables'] ?? []);
                        $this->whatsAppService->send(
                            $recipient['phone'],
                            $renderedBody
                        );

                        $results[] = [
                            'recipient' => $recipient['phone'],
                            'status' => 'sent',
                            'type' => 'whatsapp'
                        ];
                    }
                } catch (Exception $e) {
                    Log::error('Failed to send template', [
                        'recipient' => $recipient,
                        'error' => $e->getMessage()
                    ]);

                    $results[] = [
                        'recipient' => $recipient['email'] ?? $recipient['phone'],
                        'status' => 'failed',
                        'error' => $e->getMessage()
                    ];
                }
            }

            return ApiResponse(
                ['results' => $results],
                'Template sending completed'
            );
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function getContactKeys(): JsonResponse
    {
        $keys = $this->templateService->getContactVariablesKeys();
        return ApiResponse($keys, 'Contact keys retrieved successfully');
    }
}
