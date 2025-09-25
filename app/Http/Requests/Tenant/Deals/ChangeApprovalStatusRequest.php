<?php

namespace App\Http\Requests\Tenant\Deals;

use App\Enums\ApprovalStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeApprovalStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in([ApprovalStatusEnum::APPROVED->value, ApprovalStatusEnum::REJECTED->value])
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'The approval status is required.',
            'status.in' => 'The approval status must be either approved or rejected.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $dealId = $this->route('id');
            
            if ($dealId) {
                // Validate that the deal exists
                $deal = \App\Models\Tenant\Deal::find($dealId);
                if (!$deal) {
                    $validator->errors()->add('deal', 'The specified deal does not exist.');
                    return;
                }
                
                // Validate that the deal is in pending status (can only change from pending)
                if ($deal->approval_status !== ApprovalStatusEnum::PENDING->value) {
                    $validator->errors()->add('approval_status', 'Only deals with pending approval status can be changed.');
                }
            }
        });
    }
}
