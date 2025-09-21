<?php

namespace App\Services;

use App\QueryFilters\ContactFilters;
use Illuminate\Database\Eloquent\Builder;
use App\DTO\Contact\ContactDTO;
use App\DTO\Contact\ContactMergeDTO;
use App\Enums\IdenticalContactType;
use App\Enums\MergeContactType;
use App\Models\Tenant\ContactMerge;

class ContactMergeService extends BaseService
{
    public function __construct(
        public ContactMerge $model,
        public ContactService $contactService,
        public ContactPhoneService $contactPhoneService,
    ) {}

    public function getModel(): ContactMerge
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

    public function queryGet(array $filters = [], array $withRelations = []): Builder
    {
        $contacts = $this->model->with($withRelations)->orderBy('id', 'desc');
        return $contacts->filter(new ContactFilters($filters));
    }


    public function handleForm(ContactMergeDTO $contactMergeDTO)
    {
        $phones = array_column($contactMergeDTO->contact_phones, 'phone');
        if (!$contactMergeDTO->contact_id) {
            $phone_test = $contactMergeDTO->contact_id = $this->contactPhoneService->getModel()->whereIn('phone', $phones)->value('contact_id');
            $email_test = $contactMergeDTO->contact_id = $this->contactService->getModel()->where('email', $contactMergeDTO->email)->value('id');
            // dd($phone_test, $email_test);
            if ($phone_test && $email_test) {
                $contactMergeDTO->identical_contact_type = IdenticalContactType::ALL->value;
                $contactMergeDTO->contact_id = $email_test;
            } else if ($phone_test) {
                $contactMergeDTO->identical_contact_type = IdenticalContactType::PHONE->value;
                $contactMergeDTO->contact_id = $phone_test;
            } else if ($email_test) {
                $contactMergeDTO->identical_contact_type = IdenticalContactType::EMAIL->value;
                $contactMergeDTO->contact_id = $email_test;
            }
        }

        if ($contactMergeDTO->contact_id) {
            $this->model->create($contactMergeDTO->toArray());
            return true;
        } else {
            $contactDTO = ContactDTO::fromArray($contactMergeDTO->toArray());
            $this->contactService->store($contactDTO);
            return false;
        }
    }


    public function mergeList()
    {
        $contacts = $this->model->distinct()->pluck('contact_id')->toArray();
        $allContactMerges = [];
        foreach ($contacts as $contact) {
            $contactData = $this->contactService->getModel()->where('id', $contact)->first();
            $contactMergeRecords = $this->model->where('contact_id', $contact)->where('merge_status', MergeContactType::PENDING->value)->get()->toArray();
            $allContactMerges[] = [
                'contact' => $contactData,
                'contact_merge' => $contactMergeRecords,
            ];
        }
        return $allContactMerges;
    }


    public function handleMerge()
    {
        $contactMerge = $this->model->where('merge_status', MergeContactType::PENDING->value)->get()->groupBy('contact_id')->toArray();

        foreach ($contactMerge as $contact_id => $contactMergeRecords) {
            $contactData = $this->contactService->getModel()->with('contactPhones')->where('id', $contact_id)->first();

            foreach ($contactMergeRecords as $contactMergeRecord) {
                // dd($contactData->email, $contactMergeRecord['email']);
                if ($contactData->email == $contactMergeRecord['email']) {
                    foreach ($contactMergeRecord['contact_phones'] as $contactPhone) {
                        $contactPhoneExists = $this->checkContactPhone($contactPhone['phone']);
                        if ($contactPhoneExists) {
                            continue;
                        }
                        $this->contactPhoneService->storeOnePhone($contactPhone, $contactData->id);
                    }
                    $contactDTO = ContactDTO::fromArray($contactMergeRecord);
                    $this->contactService->update($contactData->id, $contactDTO);
                }

                $this->model->where('id', $contactMergeRecord['id'])->update(['merge_status' => MergeContactType::MERGED->value]);
            }
        }

        return true;
    }

    public function handleIgnore()
    {
        $this->model->where('merge_status', MergeContactType::PENDING->value)->update(['merge_status' => MergeContactType::IGNORED->value]);
        return true;
    }

    private function checkContactPhone(string $contactPhone): bool
    {
        return $this->contactPhoneService->getModel()->where('phone', $contactPhone)->exists();
    }












    public function store(ContactDTO $contactDTO)
    {
        $contactData = $contactDTO->toArray();

        // Create the contact
        $contact = $this->model->create($contactData);

        // $this->contactPhoneService->store($contactDTO->contact_phones, $contact->id);

        $contact->load('country', 'city', 'user', 'source', 'contactPhones');
        return $contact;
    }

    public function show(int $id, array $withRelations = [])
    {
        $contact = $this->findById($id, ['*'], $withRelations);
        return $contact;
    }

    public function update(int $id, ContactDTO $contactDTO)
    {
        $contact = $this->findById($id);
        if ($contactDTO->contact_phones && count($contactDTO->contact_phones) > 0) {
            // $this->contactPhoneService->update($contactDTO->contact_phones, $contact);
        }
        $contact->update($contactDTO->toArray());
        return $contact->load('contactPhones', 'country', 'city', 'user', 'source');
    }

    public function delete(int $id)
    {
        $contact = $this->findById($id);
        $contact->contactPhones()->delete();
        $contact->delete();
        return $contact;
    }

    public function index(array $filters = [], array $withRelations = [], ?int $perPage = null)
    {
        $query = $this->queryGet(filters: $filters, withRelations: $withRelations);
        if ($perPage) {
            return $query->paginate($perPage);
        }
        return $query->get();
    }
}
