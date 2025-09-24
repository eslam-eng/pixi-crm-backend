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
        $contact_id_by_email = $this->contactService->getModel()->where('email', $contactMergeDTO->email)->value('id');
        if ($contact_id_by_email) {
            $contactMergeDTO->contact_id = $contact_id_by_email;
            $contactMergeDTO->identical_contact_type = IdenticalContactType::EMAIL->value;
            $contactMergeDTO->merge_status = MergeContactType::PENDING->value;
            $this->model->create($contactMergeDTO->toArray());
            return false;
        }

        $contact_id_by_phone = $this->contactService->getModel()->whereRelation('contactPhones', 'phone', $contactMergeDTO->contact_phones[0])->value('id');
        if ($contact_id_by_phone) {
            $contactMergeDTO->contact_id = $contact_id_by_phone;
            $contactMergeDTO->identical_contact_type = IdenticalContactType::PHONE->value;
            $contactMergeDTO->merge_status = MergeContactType::PENDING->value;
            $this->model->create($contactMergeDTO->toArray());
            return false;
        }
        $contactDTO = ContactDTO::fromMergeArray($contactMergeDTO->toArray());
        $this->contactService->store($contactDTO);
        return true;
    }

    public function mergeList()
    {
        $contactsWithMerges = $this->model
            ->with('contact.contactPhones')
            ->where('merge_status', MergeContactType::PENDING->value)
            ->orderBy('id', 'desc')
            ->get();
        return $contactsWithMerges;
    }


    public function handleMerge()
    {
        $errors = [];
        $contactsMerge = $this->model->where('merge_status', MergeContactType::PENDING->value)->pluck('id');
        foreach ($contactsMerge as $contactMerge) {
            $error = $this->handleMergeById($contactMerge);
            if (is_string($error)) {
                $errors[] = $error;
            }
        }
        return $errors;
    }

    public function handleMergeById($id)
    {
        $contactMerge = $this->model
            ->where(['id' => $id, 'merge_status' => MergeContactType::PENDING->value])
            ->firstOrFail();

        $contactData = $this->contactService
            ->getModel()->with('contactPhones')
            ->where('id', $contactMerge->contact_id)->firstOrFail();


        if ($contactMerge->identical_contact_type->value == IdenticalContactType::EMAIL->value) {
            $contactDTO = ContactDTO::fromMergeArray($contactMerge->toArray());
            $phoneIsExists = $this->checkContactPhone($contactDTO->contact_phones[0]['phone']);
            $maxOfPhones = $this->checkNumberOfPhones($contactData->contactPhones->toArray());
            if ($phoneIsExists) {
                $error = 'Phone already exists for ' . $contactDTO->contact_phones[0]['phone'];
                return $error;
            } elseif ($maxOfPhones) {
                $error = 'Maximum number of phones is 5' . ' for ' . $contactDTO->email;
                return $error;
            } else {
                $this->contactService->updateMerge($contactData->id, $contactDTO);
            }
        }

        if ($contactMerge->identical_contact_type->value == IdenticalContactType::PHONE->value) {
            $contactDTO = ContactDTO::fromMergeArray($contactMerge->makeHidden(['contact_phone'])->toArray());
            $this->contactService->updateMerge($contactData->id, $contactDTO);
        }
        return $contactMerge->update(['merge_status' => MergeContactType::MERGED->value]);
    }

    public function handleIgnore()
    {
        $this->model->where('merge_status', MergeContactType::PENDING->value)->update(['merge_status' => MergeContactType::IGNORED->value]);
        return true;
    }

    public function handleIgnoreById($id)
    {
        $this->model->where('id', $id)->update(['merge_status' => MergeContactType::IGNORED->value]);
        return true;
    }

    private function checkContactPhone(string $contactPhone): bool
    {
        return $this->contactPhoneService->getModel()->where('phone', $contactPhone)->exists();
    }

    private function checkNumberOfPhones(array $contactPhones): bool
    {
        return count($contactPhones) >= 5;
    }
}
