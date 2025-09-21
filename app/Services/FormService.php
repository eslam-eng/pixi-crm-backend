<?php

namespace App\Services;

use App\Models\Tenant\Form;
use App\Models\Tenant\FormSubmission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\FormSubmissionMail;

class FormService
{
    public function createForm(array $data): Form
    {
        return DB::transaction(function () use ($data) {
            // Create form
            $form = Form::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'slug' => $data['slug'],
                'is_active' => $data['is_active'] ?? true,
            ]);

            // Create fields
            $fieldsData = collect($data['fields'])->map(function ($field, $index) {
                return array_merge($field, ['order' => $field['order'] ?? $index]);
            });

            $form->fields()->createMany($fieldsData);

            // Create actions if provided
            if (!empty($data['actions'])) {
                $actionsData = collect($data['actions'])->map(function ($action, $index) {
                    return array_merge($action, [
                        'order' => $action['order'] ?? $index,
                        'is_active' => $action['is_active'] ?? true
                    ]);
                });

                $form->actions()->createMany($actionsData);
            }

            return $form;
        });
    }

    public function submitForm(Form $form, array $data, string $ipAddress = null, string $userAgent = null): FormSubmission
    {
        // Create submission
        $submission = $form->submissions()->create([
            'data' => $data,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent
        ]);

        // Increment counter
        $form->incrementSubmissions();

        // Execute actions
        $this->executeActions($form, $submission);

        return $submission;
    }

    private function executeActions(Form $form, FormSubmission $submission): void
    {

        foreach ($form->actions as $action) {
            try {
                match ($action->type) {
                    'email' => $this->sendEmail($action->settings, $submission),
                    'webhook' => $this->triggerWebhook($action->settings, $submission),
                    'redirect' => null, // Handled in controller response
                    default => null
                };
            } catch (\Exception $e) {
                // Log error but don't fail submission
                \Log::error("Form action failed: " . $e->getMessage(), [
                    'form_id' => $form->id,
                    'action_type' => $action->type,
                    'submission_id' => $submission->id
                ]);
            }
        }
    }

    private function sendEmail(array $settings, FormSubmission $submission): void
    {
        try {
            // Validate required settings
            if (empty($settings['to'])) {
                throw new \InvalidArgumentException('Email "to" address is required');
            }

            // Validate email address
            if (!filter_var($settings['to'], FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException('Invalid email address: ' . $settings['to']);
            }

            // Queue the email
            Mail::queue(new FormSubmissionMail($submission, $settings));

            \Log::info('Form submission email queued successfully', [
                'form_id' => $submission->form_id,
                'submission_id' => $submission->id,
                'to' => $settings['to'],
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to queue form submission email', [
                'form_id' => $submission->form_id,
                'submission_id' => $submission->id,
                'error' => $e->getMessage(),
                'settings' => $settings,
            ]);

            throw $e; // Re-throw if you want the submission to fail
        }
    }

    private function triggerWebhook(array $settings, FormSubmission $submission): void
    {
        $url = $settings['url'] ?? null;

        if (!$url) return;

        try {
            $response = Http::timeout(10)
                ->retry(3, 100) // Retry 3 times with 100ms delay
                ->post($url, [
                    'form_id' => $submission->form_id,
                    'form_title' => $submission->form->title,
                    'submission_id' => $submission->id,
                    'data' => $submission->data,
                    'submitted_at' => $submission->created_at->toISOString(),
                    'ip_address' => $submission->ip_address,
                ]);

            \Log::info('Webhook triggered successfully', [
                'url' => $url,
                'status_code' => $response->status(),
                'submission_id' => $submission->id,
            ]);
        } catch (\Exception $e) {
            \Log::error('Webhook failed', [
                'url' => $url,
                'error' => $e->getMessage(),
                'submission_id' => $submission->id,
            ]);
        }
    }

    public function getRedirectUrl(Form $form): ?string
    {
        $redirectAction = $form->actions()->where('type', 'redirect')->first();
        return $redirectAction?->settings['url'] ?? null;
    }
}
