<?php

namespace App\Exports;

use App\Models\Tenant\Contact;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ContactsExport implements FromCollection, WithHeadings
{
    protected $columns;

    public function __construct(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Contact::query();

        // If user_id or city_id included, eager load relations
        if (in_array('user_id', $this->columns)) {
            $query->with('user');
        }

        if (in_array('city_id', $this->columns)) {
            $query->with('city');
        }

        if (in_array('country_id', $this->columns)) {
            $query->with('country');
        }

        if (in_array('source_id', $this->columns)) {
            $query->with('source');
        }

        $contacts = $query->select($this->columns)->get();

        return $contacts->map(function ($contact) {
            $row = [];

            foreach ($this->columns as $column) {
                if ($column === 'user_id') {
                    $row['user_id'] = $contact->user->first_name . ' ' . $contact->user->last_name ?? ''; // replace ID with name
                } elseif ($column === 'city_id') {
                    $row['city_id'] = $contact->city->name ?? ''; // replace ID with name
                } elseif ($column === 'country_id') {
                    $row['country_id'] = $contact->country->name ?? ''; // replace ID with name
                } elseif ($column === 'source_id') {
                    $row['source_id'] = $contact->source->name ?? ''; // replace ID with name
                } elseif ($column === 'tags') {
                    $row['tags'] = $this->formatTags($contact->tags);
                } elseif ($column === 'email_permission') {
                    $row['email_permission'] = $contact->email_permission ? 'Yes' : 'No';
                } elseif ($column === 'phone_permission') {
                    $row['phone_permission'] = $contact->phone_permission ? 'Yes' : 'No';
                } elseif ($column === 'whatsapp_permission') {
                    $row['whatsapp_permission'] = $contact->whatsapp_permission ? 'Yes' : 'No';
                } else {
                    $row[$column] = $contact->$column;
                }
            }

            return $row;
        });
    }

    public function headings(): array
    {
        return array_map(function ($column) {
            if ($column === 'user_id') {
                return 'Owner';
            } elseif ($column === 'city_id') {
                return 'City';
            } elseif ($column === 'country_id') {
                return 'Country';
            } elseif ($column === 'source_id') {
                return 'Source';
            }
            return ucfirst(str_replace('_', ' ', $column));
        }, $this->columns);
    }

    private function formatTags($tags): string
    {
        if (empty($tags)) {
            return '';
        }

        // If it's already an array (due to casting)
        if (is_array($tags)) {
            return implode(', ', $tags);
        }

        // If it's a JSON string
        if (is_string($tags)) {
            $decoded = json_decode($tags, true);
            return is_array($decoded) ? implode(', ', $decoded) : '';
        }

        return '';
    }
}
