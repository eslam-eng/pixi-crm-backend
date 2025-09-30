<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormSubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'submission_id' => $this->id,
            'form' => new FormResource($this->whenLoaded('form')),
            'data' => $this->data,
            'submitted_at' => $this->created_at
        ];
    }
}
