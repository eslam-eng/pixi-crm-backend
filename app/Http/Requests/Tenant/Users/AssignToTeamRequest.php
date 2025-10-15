<?php

namespace App\Http\Requests\Tenant\Users;

use Illuminate\Foundation\Http\FormRequest;

class AssignToTeamRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        dd('jkd');
        return [
            'team_id' => [
                'required',
                'exists:teams,id',
            ],
            'user_id' => [
                'required',
                'exists:users,id',
            ],
            'monthly_target' => 'nullable|array',
            'monthly_target.*.amount' => 'required|numeric|min:1',
            'monthly_target.*.month' => 'required|distinct|integer|min:1|max:12',
            'quarterly_target' => 'nullable|array',
            'quarterly_target.*.amount' => 'required|numeric|min:1',
            'quarterly_target.*.quarter' => 'required|distinct|integer|min:1|max:4',
        ];
    }
}
