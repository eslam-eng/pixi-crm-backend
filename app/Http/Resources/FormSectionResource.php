<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormSectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'module' => $this->module,
            'key' => $this->key,
            'name' => $this->name,
            'ordering' => $this->ordering,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
