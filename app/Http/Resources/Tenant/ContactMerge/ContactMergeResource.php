<?php

namespace App\Http\Resources\Tenant\ContactMerge;

use App\Http\Resources\ContactResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactMergeResource extends JsonResource
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
            'contact' => $this->whenLoaded('contact', fn() => new ContactResource($this->contact)),
            'identical_contact_type' => $this->identical_contact_type,
            'merge_status' => $this->merge_status,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'contact_phones' => $this->contact_phones,
            'job_title' => $this->job_title,
            'department' => $this->department,
            'status' => $this->status,
            'source_id' => $this->source_id,
            'contact_method' => $this->contact_method,
            'campaign_name' => $this->campaign_name,
            'email_permission' => $this->email_permission,
            'phone_permission' => $this->phone_permission,
            'whatsapp_permission' => $this->whatsapp_permission,
            'company_name' => $this->company_name,
            'total_amount' => $this->total_amount,
        
        ];
    }
}
