<?php

namespace App\Http\Requests\Tenant\Template;

use App\Http\Requests\BaseRequest;

class SendTemplateRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'template_slug' => 'required|string|exists:templates,slug',
            'type' => 'nullable|string|in:email,whatsapp',
            'recipients' => 'required|array|min:1',
            'recipients.*.email' => 'required_without:recipients.*.phone|email',
            'recipients.*.phone' => 'required_without:recipients.*.email|string',
            'recipients.*.name' => 'nullable|string',
            'variables' => 'nullable|array',
        ];
    }

    public function validatedData(): array
    {
        return $this->validated();
    }
}

