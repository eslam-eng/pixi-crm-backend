<?php

namespace App\Http\Requests\Tenant\Deals;

use App\Models\Tenant\Deal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DealPaymentRequest extends FormRequest
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
        $dealId = $this->route('dealId');
        
        return [
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) use ($dealId) {
                    if ($dealId) {
                        $deal = Deal::find($dealId);
                        if ($deal) {
                            // Check if deal has amount due
                            if ($deal->amount_due <= 0) {
                                $fail('This deal has no amount due. Payment cannot be processed.');
                                return;
                            }
                            
                            // Check if payment amount exceeds amount due
                            if ($value > $deal->amount_due) {
                                $fail('Payment amount cannot exceed the amount due (' . number_format($deal->amount_due, 2) . ').');
                                return;
                            }
                        }
                    }
                }
            ],
            'pay_date' => 'required|date|before_or_equal:today',
            'payment_method_id' => 'required|integer|exists:payment_methods,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'The payment amount is required.',
            'amount.numeric' => 'The payment amount must be a valid number.',
            'amount.min' => 'The payment amount must be at least 0.01.',
            'pay_date.required' => 'The payment date is required.',
            'pay_date.date' => 'The payment date must be a valid date.',
            'pay_date.before_or_equal' => 'The payment date cannot be in the future.',
            'payment_method_id.required' => 'The payment method is required.',
            'payment_method_id.integer' => 'The payment method must be a valid ID.',
            'payment_method_id.exists' => 'The selected payment method does not exist.',
        ];
    }
}
