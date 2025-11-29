<?php

namespace App\Services;

use App\DTO\Contact\ContactPhoneDTO;
use App\Models\Tenant\ContactMerge;
use App\Models\Tenant\ContactMergePhone;
use App\QueryFilters\ContactFilters;
use Illuminate\Database\Eloquent\Builder;

class ContactMergePhoneService extends BaseService
{
    public function __construct(
        public ContactMergePhone $model,
    ) {}

    public function getModel(): ContactMergePhone
    {
        return $this->model;
    }

    public function getAll(array $filters = [])
    {
        return $this->queryGet($filters)->get();
    }

    public function getTableName(): string
    {
        return $this->getModel()->getTable();
    }

    public function listing(array $filters = [], array $withRelations = [], $perPage = 5): \Illuminate\Contracts\Pagination\CursorPaginator
    {
        return $this->queryGet(filters: $filters, withRelations: $withRelations)->cursorPaginate($perPage);
    }

    public function queryGet(array $filters = [], array $withRelations = []): Builder
    {
        $contacts = $this->model->with($withRelations)->orderBy('id', 'desc');
        return $contacts->filter(new ContactFilters($filters));
    }

    public function store(array $phones, int $contactId)
    {
        foreach ($phones as $phone) {
            $this->model->create([
                'phone' => $phone,
                'is_primary' => false,
                'enable_whatsapp' => false,
                'contact_merge_id' => $contactId
            ]);
        }
    }

    public function storeOnePhone(array $phones, int $contactId)
    {
        $this->model->create([
            'phone' => $phones['phone'],
            'is_primary' => $phones['is_primary'],
            'enable_whatsapp' => $phones['enable_whatsapp'],
            'contact_merge_id' => $contactId
        ]);
    }

    public function update(array $contactPhones, ContactMerge $contact): void
    {
        $this->syncContactPhones($contact, $contactPhones);
    }


    private function syncContactPhones(ContactMerge $contact, array $contactPhones)
    {
        // Get existing phones
        $existingPhones = $contact->contactMergePhones()->get();
        $existingPhoneNumbers = $existingPhones->pluck('phone')->toArray();

        // Process new phones
        $newPhoneNumbers = collect($contactPhones)->pluck('phone')->toArray();

        // Find phones to delete, add, and update
        $phonesToDelete = array_diff($existingPhoneNumbers, $newPhoneNumbers);
        $phonesToAdd = array_diff($newPhoneNumbers, $existingPhoneNumbers);

        // Delete removed phones
        if (!empty($phonesToDelete)) {
            $contact->contactMergePhones()
                ->whereIn('phone', $phonesToDelete)
                ->delete();
        }

        // Add or update phones
        foreach ($contactPhones as $phoneData) {
            $phoneDTO = ContactPhoneDTO::fromArray($phoneData);
            $contact->contactMergePhones()->updateOrCreate(
                ['phone' => $phoneDTO->phone],
                [
                    'is_primary' => $phoneDTO->is_primary,
                    'enable_whatsapp' => $phoneDTO->enable_whatsapp,
                ]
            );
        }
    }
}
