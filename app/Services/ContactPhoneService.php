<?php

namespace App\Services;

use App\DTO\Contact\ContactPhoneDTO;
use App\Models\Tenant\Contact;
use App\Models\Tenant\ContactPhone;
use App\QueryFilters\ContactFilters;
use Illuminate\Database\Eloquent\Builder;

class ContactPhoneService extends BaseService
{
    public function __construct(
        public ContactPhone $model,
    ) {}

    public function getModel(): ContactPhone
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
                'phone' => $phone['phone'],
                'is_primary' => $phone['is_primary'] ?? false,
                'enable_whatsapp' => $phone['enable_whatsapp'] ?? false,
                'contact_id' => $contactId
            ]);
        }
    }

    public function storeOnePhone(array $phone, int $contactId)
    {
        $this->model->create([
            'phone' => $phone['phone'],
            'is_primary' => $phone['is_primary'],
            'enable_whatsapp' => $phone['enable_whatsapp'],
            'contact_id' => $contactId
        ]);
    }

    public function update(array $contactPhones, Contact $Contact): void
    {
        $this->syncContactPhones($Contact, $contactPhones);
    }


    private function syncContactPhones(Contact $contact, array $contactPhones)
    {
        // Get existing phones
        $existingPhones = $contact->contactPhones()->get();
        $existingPhoneNumbers = $existingPhones->pluck('phone')->toArray();

        // Process new phones
        $newPhoneNumbers = collect($contactPhones)->pluck('phone')->toArray();

        // Find phones to delete, add, and update
        $phonesToDelete = array_diff($existingPhoneNumbers, $newPhoneNumbers);
        $phonesToAdd = array_diff($newPhoneNumbers, $existingPhoneNumbers);

        // Delete removed phones
        if (!empty($phonesToDelete)) {
            $contact->contactPhones()
                ->whereIn('phone', $phonesToDelete)
                ->delete();
        }

        // Add or update phones
        foreach ($contactPhones as $phoneData) {
            $phoneDTO = ContactPhoneDTO::fromArray($phoneData);
            $contact->contactPhones()->updateOrCreate(
                ['phone' => $phoneDTO->phone],
                [
                    'is_primary' => $phoneDTO->is_primary,
                    'enable_whatsapp' => $phoneDTO->enable_whatsapp,
                ]
            );
        }
    }
}
