<?php
// app/Http/Resources/FormResource.php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'slug' => $this->slug,
            'is_active' => $this->is_active,
            'submissions_count' => $this->submissions_count,
            'fields' => FormFieldResource::collection($this->whenLoaded('fields')),
            'actions' => FormActionResource::collection($this->whenLoaded('actions')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}