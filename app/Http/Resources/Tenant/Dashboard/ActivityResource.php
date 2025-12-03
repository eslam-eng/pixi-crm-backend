<?php
namespace App\Http\Resources\Tenant\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this['id'],
            'description' => __('app.' . $this->description),
            'type' => __('app.' . $this->subject_type),
            'time' => $this->created_at->diffForHumans(),
            'user' => $this->causer ? $this->causer->first_name.' '.$this->causer->last_name : __('app.system'),
        ];
    }
}
