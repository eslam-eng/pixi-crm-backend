<?php

namespace App\Services;

use App\QueryFilters\ContactFilters;
use Illuminate\Database\Eloquent\Builder;
use App\DTO\Contact\ContactDTO;
use App\DTO\Contact\ContactMergeDTO;
use App\Enums\IdenticalContactType;
use App\Enums\MergeContactType;
use App\Exceptions\GeneralException;
use App\Models\Tenant\ContactMerge;

class ContactMergeService extends BaseService
{
    public function __construct(
        public ContactMerge $model,
        public ContactService $contactService,
        public ContactPhoneService $contactPhoneService,
        public ContactMergePhoneService $contactMergePhoneService,
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
            $contactMerge = $this->model->create($contactMergeDTO->toArray());
            $this->contactMergePhoneService->store($contactMergeDTO->contact_phones, $contactMerge->id);
            return false;
        }

        $contact_id_by_phone = $this->contactService->getModel()->whereRelation('contactPhones', 'phone', $contactMergeDTO->contact_phones[0])->value('id');
        if ($contact_id_by_phone) {
            $contactMergeDTO->contact_id = $contact_id_by_phone;
            $contactMergeDTO->identical_contact_type = IdenticalContactType::PHONE->value;
            $contactMergeDTO->merge_status = MergeContactType::PENDING->value;
            $contactMerge = $this->model->create($contactMergeDTO->toArray());
            $this->contactMergePhoneService->store($contactMergeDTO->contact_phones, $contactMerge->id);
            return false;
        }

        $newContact = $this->contactService->storeMerge($contactMergeDTO);

        $contactPhones = collect($contactMergeDTO->contact_phones)->map(function ($phone) {
            return [
                'phone' => $phone,
                'is_primary' => false,
                'enable_whatsapp' => false,
            ];
        })->toArray();

        $this->contactPhoneService->store($contactPhones, $newContact->id);
        return true;
    }

    public function mergeList()
    {
        $contactsWithMerges = $this->model
            ->with('contact.contactPhones', 'contactMergePhones')
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
        $contactMerge = $this->model->with('contactMergePhones')
            ->where(['id' => $id, 'merge_status' => MergeContactType::PENDING->value])
            ->firstOrFail();

        $contactData = $this->contactService
            ->getModel()->with('contactPhones')
            ->where('id', $contactMerge->contact_id)->firstOrFail();
        
        if ($contactMerge->identical_contact_type->value === IdenticalContactType::EMAIL->value) {
            $contactDTO = ContactDTO::fromMergeArray($contactMerge->toArray());
            $phoneIsExists = $this->checkContactPhone($contactDTO->contact_merge_phones);
            $maxOfPhones = $this->checkNumberOfPhones($contactDTO->contact_merge_phones, $contactData->contactPhones->count());
            if ($phoneIsExists) {
                throw new GeneralException('Phone already exists for ' . collect($contactDTO->contact_merge_phones)->pluck('phone')->toArray()[0]);
            } elseif ($maxOfPhones) {
                throw new GeneralException('Maximum number of phones is 5' . ' for ' . $contactDTO->email);
            }
            $this->contactService->updateMerge($contactData->id, $contactDTO);
        }elseif ($contactMerge->identical_contact_type->value === IdenticalContactType::PHONE->value) {
            $contactDTO = ContactDTO::fromMergeArray($contactMerge->makeHidden(['contact_phone'])->toArray());
            $phoneIsExists = $this->checkContactPhone($contactDTO->contact_merge_phones);
            $maxOfPhones = $this->checkNumberOfPhones($contactDTO->contact_merge_phones, $contactData->contactPhones->count());
            if ($phoneIsExists) {
                throw new GeneralException('Phone already exists for ' . collect($contactDTO->contact_merge_phones)->pluck('phone')->toArray()[0]);
            } elseif ($maxOfPhones) {
                throw new GeneralException('Maximum number of phones is 5' . ' for ' . $contactDTO->email);
            }
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

    private function checkContactPhone(array $contactPhones): bool
    {
        return $this->contactPhoneService->getModel()->whereIn('phone', collect($contactPhones)->pluck('phone')->toArray())->exists();
    }

    private function checkNumberOfPhones(array $contactPhones, int $totalNumberOfPhones): bool
    {
        return (collect($contactPhones)->count() + $totalNumberOfPhones) >= 5;
    }
}
