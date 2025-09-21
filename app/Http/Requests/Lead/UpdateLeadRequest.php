<?php

namespace App\Http\Requests\Lead;

use App\DTO\Lead\LeadDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLeadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'contact_id' => 'required|exists:contacts,id',
            'reason_id'  => 'nullable|exists:reasons,id',
            'user_id'    => 'required|exists:users,id',
            'value'      => 'nullable|numeric|min:0',
            'status'     => 'required|in:open,lost,won,abandoned',
            'industries' => 'nullable|array',
            'industries.*' => 'integer|exists:industries,id',
            'services' => 'nullable|array',
            'services.*.service_id' => 'integer|exists:services,id',
            'services.*.category_id' => 'nullable|integer|exists:categories,id',
            'customFields' => 'nullable|array',
            'customFields.*.custom_field_id' => 'integer|exists:custom_fields,id',
            'customFields.*.value' => 'required|string',
            'stage_id' => 'nullable|exists:stages,id',
            'pipeline_id' => 'nullable|exists:pipelines,id',
        ];
    }

    public function toLeadDTO(): LeadDTO
    {
        return LeadDTO::fromRequest($this);
    }
}
