<?php

namespace App\Services;

use App\Models\Tenant\Contact;
use App\QueryFilters\ContactFilters;
use Illuminate\Database\Eloquent\Builder;
use App\DTO\Contact\ContactDTO;
use Excel;

class ContactService extends BaseService
{
    public function __construct(
        public Contact $model,
        public ContactPhoneService $contactPhoneService,
    ) {}

    public function getModel(): Contact
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

    public function datatable(array $filters = [], array $withRelations = [])
    {
        $contacts = $this->getQuery()->with($withRelations);
        return $contacts->filter(new ContactFilters($filters));
    }

    public function store(ContactDTO $contactDTO)
    {
        // Create the contact
        $contact = $this->model->create($contactDTO->toArray());
        $this->contactPhoneService->store($contactDTO->contact_phones, $contact->id);

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
            $this->contactPhoneService->update($contactDTO->contact_phones, $contact);
        }
        $contact->update($contactDTO->toArray());
        return $contact->load('contactPhones', 'country', 'city', 'user', 'source');
    }

    public function updateMerge(int $id, ContactDTO $contactDTO)
    {
        $contact = $this->findById($id);
        if ($contactDTO->contact_phones && count($contactDTO->contact_phones) > 0) {
            $this->contactPhoneService->store($contactDTO->contact_phones, $contact->id);
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

    public function getDatabaseFields()
    {
        return [
            'first_name' => [
                'label' => 'First Name',
                'required' => true,
                'type' => 'string'
            ],
            'last_name' => [
                'label' => 'Last Name',
                'required' => true,
                'type' => 'string'
            ],
            'email' => [
                'label' => 'Email Address',
                'required' => true,
                'type' => 'email'
            ],
            'business_phone' => [
                'label' => 'Business Phone',
                'required' => false,
                'type' => 'string'
            ],
            'mobile_phone' => [
                'label' => 'Mobile Phone',
                'required' => false,
                'type' => 'string'
            ],
            'job_title' => [
                'label' => 'Job Title',
                'required' => false,
                'type' => 'string'
            ],
            'department' => [
                'label' => 'Department',
                'required' => false,
                'type' => 'string'
            ],
            'status' => [
                'label' => 'Status',
                'required' => false,
                'type' => 'select',
                'options' => ['active', 'inactive', 'pending']
            ],
            'contact_method' => [
                'label' => 'Preferred Contact Method',
                'required' => false,
                'type' => 'select',
                'options' => ['email', 'phone', 'whatsapp', 'meeting']
            ],
            'email_permission' => [
                'label' => 'Email Permission',
                'required' => false,
                'type' => 'boolean'
            ],
            'phone_permission' => [
                'label' => 'Phone Permission',
                'required' => false,
                'type' => 'boolean'
            ],
            'whatsapp_permission' => [
                'label' => 'WhatsApp Permission',
                'required' => false,
                'type' => 'boolean'
            ],
            'company_name' => [
                'label' => 'Company Name',
                'required' => false,
                'type' => 'string'
            ],
            'website' => [
                'label' => 'Website',
                'required' => false,
                'type' => 'url'
            ],
            'industry' => [
                'label' => 'Industry',
                'required' => false,
                'type' => 'string'
            ],
            'company_size' => [
                'label' => 'Company Size',
                'required' => false,
                'type' => 'string'
            ],
            'address' => [
                'label' => 'Address',
                'required' => false,
                'type' => 'text'
            ],
            'country_id' => [
                'label' => 'Country',
                'required' => false,
                'type' => 'select',
                'options_source' => 'countries'
            ],
            'city_id' => [
                'label' => 'City',
                'required' => false,
                'type' => 'select',
                'options_source' => 'cities'
            ],
            'state' => [
                'label' => 'State/Province',
                'required' => false,
                'type' => 'string'
            ],
            'zip_code' => [
                'label' => 'ZIP/Postal Code',
                'required' => false,
                'type' => 'string'
            ],
            'tags' => [
                'label' => 'Tags',
                'required' => false,
                'type' => 'string'
            ],
            'notes' => [
                'label' => 'Notes',
                'required' => false,
                'type' => 'text'
            ]
        ];
    }

    public function suggestMapping(array $excelHeaders, array $databaseFields)
    {
        $mapping = [];

        foreach (array_keys($databaseFields) as $dbField) {
            $mapping[$dbField] = $this->findBestMatch($dbField, $excelHeaders);
        }

        return $mapping;
    }

    private function findBestMatch($dbField, array $excelHeaders)
    {
        // Exact match
        if (in_array($dbField, $excelHeaders)) {
            return $dbField;
        }

        // Common variations
        $variations = [
            'first_name' => ['firstname', 'fname', 'first', 'given_name'],
            'last_name' => ['lastname', 'lname', 'last', 'surname', 'family_name'],
            'email' => ['email_address', 'e_mail', 'mail'],
            'business_phone' => ['work_phone', 'office_phone', 'business_number'],
            'mobile_phone' => ['cell_phone', 'mobile', 'cell', 'mobile_number'],
            'company_name' => ['company', 'organization', 'org', 'business_name'],
            'job_title' => ['title', 'position', 'role'],
            'zip_code' => ['zip', 'postal_code', 'postcode'],
        ];

        if (isset($variations[$dbField])) {
            foreach ($variations[$dbField] as $variation) {
                if (in_array($variation, $excelHeaders)) {
                    return $variation;
                }
            }
        }

        // Fuzzy matching (contains)
        foreach ($excelHeaders as $header) {
            if (strpos(strtolower($header), strtolower($dbField)) !== false) {
                return $header;
            }
        }

        return null;
    }

    public function getPreviewData($file, $rows = 5)
    {
        try {
            $data = Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray {
                public function array(array $array)
                {
                    return $array;
                }
            }, $file);

            $sheetData = $data[0] ?? [];
            $headers = $sheetData[0] ?? [];
            $previewRows = array_slice($sheetData, 1, $rows);

            return [
                'headers' => $headers,
                'rows' => $previewRows
            ];
        } catch (\Exception $e) {
            return ['headers' => [], 'rows' => []];
        }
    }

    public function get_statistics()
    {
        $total_contacts = $this->model->count();
        $active_contacts = $this->model->where('status', 'active')->count();
        $inactive_contacts = $this->model->where('status', 'inactive')->count();
        $pending_contacts = $this->model->where('status', 'pending')->count();

        return [
            'total_contacts' => $total_contacts,
            'active_contacts' => $active_contacts,
            'inactive_contacts' => $inactive_contacts,
            'pending_contacts' => $pending_contacts
        ];
    }

    private function syncContactPhones(Contact $contact, array $newNumbers): void
    {
        // Get existing contact numbers
        $existingPhones = $contact->contactPhones()->pluck('phone')->toArray();

        // Determine what to add, keep, and remove
        $numbersToAdd = array_diff($newNumbers, $existingPhones);
        $numbersToRemove = array_diff($existingPhones, $newNumbers);

        // Remove numbers that are no longer needed
        if (!empty($numbersToRemove)) {
            $contact->contactPhones()
                ->whereIn('phone', $numbersToRemove)
                ->delete();
        }

        // Add new numbers
        if (!empty($numbersToAdd)) {
            $contactPhonesData = collect($numbersToAdd)->map(function ($number) {
                return ['phone' => $number];
            })->toArray();

            $contact->contactPhones()->createMany($contactPhonesData);
        }
    }
}
