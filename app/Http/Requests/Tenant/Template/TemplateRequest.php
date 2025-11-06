<?php

namespace App\Http\Requests\Tenant\Template;

use App\Http\Requests\BaseRequest;

class TemplateRequest extends BaseRequest
{
    public function rules(): array
    {
        $templateId = $this->route('template');
        
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:templates,slug,' . $templateId,
            'type' => 'required|string|in:email,whatsapp',
            'subject' => 'nullable|string|max:255|required_if:type,email',
            'body' => 'required|string',
            'variables' => 'nullable|array',
            'variables.*' => 'string',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function validatedData(): array
    {
        return $this->validated();
    }
}

