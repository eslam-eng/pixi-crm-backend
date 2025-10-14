<?php

namespace App\Http\Requests\Tenant\Users;

use App\Models\Tenant\Team;
use App\Models\Tenant\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignToTeamRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $user = $this->getRequestUser();
        if (!$user) {
            return []; // Return empty rules if chair doesn't exist
        }

        $rules = [
            'team_id' => [
                'required',
                'exists:teams,id',
                // Prevent assigning to same team if already active
                Rule::unique('chairs')->where(function ($query) use ($user) {
                    return $query->where('user_id', $user->id)
                        ->whereNull('ended_at');
                })
            ],
            'started_at' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            // 'Quarterly_target' => ['array', 'nullable'],
            // 'target.*.amount' => ['required_with:target', 'numeric', 'min:0'],
            // 'target.*.quarter' => ['required_with:target', 'string', 'size:3'],
        ];


        $team = $this->getTeam();

        if ($team && $team->is_target) {
            $rules['monthly_target'] = ['required', 'array', 'min:1'];
            $rules['monthly_target.*.amount'] = ['required', 'numeric', 'min:0'];
            $rules['monthly_target.*.month'] = ['required', 'integer', 'min:1', 'max:12'];

            $rules['quarterly_target'] = ['nullable', 'array', 'min:1'];
            $rules['quarterly_target.*.amount'] = ['required', 'numeric', 'min:0'];
            $rules['quarterly_target.*.quarter'] = ['required', 'string'];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'team_id.required' => 'Please select a team.',
            'team_id.exists' => 'The selected team does not exist.',
            'team_id.unique' => 'This user is already assigned to this team. Please end the current assignment first.',
            'started_at.required' => 'Start date is required.',
            'started_at.date' => 'Start date must be a valid date.',
            'started_at.before_or_equal' => 'Start date cannot be in the future.',

            'monthly_target.required' => 'Monthly targets are required for target teams.',
            'monthly_target.array' => 'Monthly targets must be provided as an array.',
            'monthly_target.min' => 'At least one monthly target is required for target teams.',
            'monthly_target.*.amount.required' => 'Target amount is required.',
            'monthly_target.*.amount.numeric' => 'Target amount must be a number.',
            'monthly_target.*.amount.min' => 'Target amount must be at least 0.',
            'monthly_target.*.month.required' => 'Month is required.',
            'monthly_target.*.month.integer' => 'Month must be a number between 1 and 12.',
            'monthly_target.*.month.min' => 'Month must be at least 1.',
            'monthly_target.*.month.max' => 'Month cannot be greater than 12.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            if (!$this->getRequestUser()) {
                $validator->errors()->add('user', 'The specified user does not exist.');
                return;
            }

            if (!$this->checkIsTarget() && !blank($this->monthly_target)) {
                $validator->errors()->add('team', 'The specified team does\'t have target.');
                return;
            }

            // Check for overlapping assignments
            if ($this->hasOverlappingAssignment()) {
                $validator->errors()->add('team_id', 'This user already has an active assignment during this period.');
            }

            if ($this->justOneTimeWithoutEndDate()) {
                $validator->errors()->add('user', 'The specified user exist in team.');
                return;
            }
        });
    }

    private function hasOverlappingAssignment()
    {
        $user = $this->getRequestUser();
        if (!$user) {
            return []; // Return empty rules if chair doesn't exist
        }

        $query = \App\Models\Tenant\Chair::where('user_id', $user->id)
            ->where('team_id', $this->team_id);

        if ($this->filled('ended_at')) {
            // Check for overlap with date range
            $query->where(function ($q) {
                $q->whereNull('ended_at')
                    ->orWhere(function ($subq) {
                        $subq->where('started_at', '<=', $this->ended_at)
                            ->where('ended_at', '>=', $this->started_at);
                    });
            });
        } else {
            // Check for any active assignment
            $query->whereNull('ended_at')
                ->where('started_at', '<=', now());
        }
        return $query->exists();
    }

    private function justOneTimeWithoutEndDate()
    {
        $user = $this->getRequestUser();
        if (!$user) {
            return []; // Return empty rules if chair doesn't exist
        }

        $query = \App\Models\Tenant\Chair::where('user_id', $user->id)
            ->where('ended_at', null);

        return $query->exists();
    }


    private function getRequestUser()
    {
        try {
            return User::findOrFail($this->route('user'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return null;
        }
    }

    private function getTeam()
    {
        try {
            return Team::findOrFail($this->team_id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return null;
        }
    }

    private function checkIsTarget()
    {
        $team = $this->getTeam();
        return $team->is_target ?? false;
    }
}
