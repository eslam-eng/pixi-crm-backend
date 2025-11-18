<?php

namespace App\DTO\Contact;

use App\DTO\BaseDTO;
use Illuminate\Support\Arr;

class ContactMergeDTO extends BaseDTO
{
    public function __construct(
        public ?string $contact_id,
        public ?string $identical_contact_type,
        public ?string $merge_status,
        public ?string $first_name,
        public ?string $last_name,
        public ?string $email,
        public ?array  $contact_phones,
        public ?string $job_title,
        public ?string $department,
        public ?string $status,
        public ?string $source_id,
        public ?bool   $contact_method,
        public ?string $campaign_name,
        public ?bool   $email_permission,
        public ?bool   $phone_permission,
        public ?string $whatsapp_permission,
        public ?string $company_name,
        public ?string $website,
        public ?string $industry,
        public ?string $company_size,
        public ?string $address,
        public ?string $country_id,
        public ?string $city_id,
        public ?string $state,
        public ?string $zip_code,
        public ?string $user_id,
        public ?string $tags = null,
        public ?string $notes = null,
    ) {}

    public static function fromRequest($request): BaseDTO
    {
        return new self(
            contact_id: $request->input('contact_id'),
            identical_contact_type: $request->input('identical_contact_type'),
            merge_status: $request->input('merge_status'),
            first_name: $request->input('first_name'),
            last_name: $request->input('last_name'),
            email: $request->input('email'),
            contact_phones: $request->input('contact_phones'),
            job_title: $request->input('job_title'),
            department: $request->input('department'),
            status: $request->input('status'),
            source_id: $request->input('source_id'),
            contact_method: $request->input('contact_method'),
            campaign_name: $request->input('campaign_name'),
            email_permission: $request->input('email_permission'),
            phone_permission: $request->input('phone_permission'),
            whatsapp_permission: $request->input('whatsapp_permission'),
            company_name: $request->input('company_name'),
            website: $request->input('website'),
            industry: $request->input('industry'),
            company_size: $request->input('company_size'),
            address: $request->input('address'),
            country_id: $request->input('country_id'),
            city_id: $request->input('city_id'),
            state: $request->input('state'),
            zip_code: $request->input('zip_code'),
            user_id: $request->input('user_id'),
            tags: json_encode($request->input('tags') ?? []),
            notes: $request->input('notes'),
        );
    }

    public function toArray(): array
    {
        return [
            'contact_id' => $this->contact_id,
            'identical_contact_type' => $this->identical_contact_type,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
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
            'website' => $this->website,
            'industry' => $this->industry,
            'company_size' => $this->company_size,
            'address' => $this->address,
            'country_id' => $this->country_id,
            'city_id' => $this->city_id,
            'state' => $this->state,
            'zip_code' => $this->zip_code,
            'user_id' => $this->user_id,
            'tags' => $this->tags ?? [],
            'notes' => $this->notes,
        ];
    }

    public function toContactArray(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
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
            'website' => $this->website,
            'industry' => $this->industry,
            'company_size' => $this->company_size,
            'address' => $this->address,
            'country_id' => $this->country_id,
            'city_id' => $this->city_id,
            'state' => $this->state,
            'zip_code' => $this->zip_code,
            'user_id' => $this->user_id,
            'tags' => $this->tags,
            'notes' => $this->notes,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            contact_id: Arr::get($data, 'contact_id'),
            identical_contact_type: Arr::get($data, 'identical_contact_type'),
            merge_status: Arr::get($data, 'merge_status'),
            first_name: Arr::get($data, 'first_name'),
            last_name: Arr::get($data, 'last_name'),
            email: Arr::get($data, 'email'),
            contact_phones: Arr::get($data, 'contact_phones'),
            job_title: Arr::get($data, 'job_title'),
            department: Arr::get($data, 'department'),
            status: Arr::get($data, 'status'),
            source_id: Arr::get($data, 'source_id'),
            contact_method: Arr::get($data, 'contact_method'),
            campaign_name: Arr::get($data, 'campaign_name'),
            email_permission: Arr::get($data, 'email_permission'),
            phone_permission: Arr::get($data, 'phone_permission'),
            whatsapp_permission: Arr::get($data, 'whatsapp_permission'),
            company_name: Arr::get($data, 'company_name'),
            website: Arr::get($data, 'website'),
            industry: Arr::get($data, 'industry'),
            company_size: Arr::get($data, 'company_size'),
            address: Arr::get($data, 'address'),
            country_id: Arr::get($data, 'country_id'),
            city_id: Arr::get($data, 'city_id'),
            state: Arr::get($data, 'state'),
            zip_code: Arr::get($data, 'zip_code'),
            user_id: Arr::get($data, 'user_id'),
            tags: Arr::get($data, 'tags') ?? [],
            notes: Arr::get($data, 'notes'),
        );
    }
}
