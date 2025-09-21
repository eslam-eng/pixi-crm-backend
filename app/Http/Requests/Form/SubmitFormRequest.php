<?php
    
namespace App\Http\Requests\Form;

use Illuminate\Foundation\Http\FormRequest;

class SubmitFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Dynamic rules will be added in the controller
        return [];
    }
}