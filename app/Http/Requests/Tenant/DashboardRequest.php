<?php

namespace App\Http\Requests\Tenant;

use App\Models\Tenant\User;
use Illuminate\Foundation\Http\FormRequest;

class DashboardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id',
            'team_id' => 'nullable|exists:teams,id',
            'pipeline_id' => 'nullable|exists:pipelines,id',
        ];
    }

    public function prepareForValidation()
    {
        $user = auth()->user();
        $role = $user->roles[0]->name;

        $filters = [];

        switch ($role) {
            case 'admin':
                break;
            case 'manager':
                $filters['team_id'] = $user->team_id;

                if ($this->user_id) {
                    $teamUserIds = User::where('team_id', $user->team_id)->pluck('id');
                    if ($teamUserIds->contains($this->user_id)) {
                        $filters['user_id'] = $this->user_id;
                    } else {
                        $filters['user_id'] = $user->id;
                    }
                }
                break;

            default:
                $filters['user_id'] = $user->id;
                $filters['team_id'] = null;
                break;
        }

        if ($this->user_id) {
            $filters['team_id'] = null;
        }

        $this->merge($filters);
    }
}
