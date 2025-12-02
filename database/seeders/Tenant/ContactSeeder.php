<?php

namespace Database\Seeders\Tenant;

use App\Enums\CompanySizes;
use App\Enums\ContactMethods;
use App\Enums\ContactStatus;
use App\Enums\IndustryStatus;
use App\Models\Tenant\Contact;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Contact::count() > 0) return;
        $contact1 = Contact::updateOrCreate(
            ['email' => 'john.doe@example.com'],
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'job_title' => 'Software Engineer',
                'status' => ContactStatus::ACTIVE,
                'email' => 'john.doe@example.com',
                'source_id' => 1,
                'contact_method' => ContactMethods::EMAIL,
                'email_permission' => 1,
                'phone_permission' => 1,
                'whatsapp_permission' => 1,
                'company_name' => 'Example Inc.',
                'campaign_name' => 'Example Campaign',
                'website' => 'https://example.com',
                'industry' => IndustryStatus::TECHNOLOGY,
                'company_size' => CompanySizes::LEVEL_1,
                'address' => '123 Main St, Anytown, USA',
                'country_id' => 1,
                'city_id' => 1,
                'state' => 'CA',
                'zip_code' => '12345',
                'user_id' => 1,
                'notes' => 'Notes',
                'tags' => json_encode(['tag1', 'tag2']),
            ]
        );

        $contact1->contactPhones()->createMany([
            [
                'phone' => '1234567890',
                'is_primary' => true,
                'enable_whatsapp' => true,
            ],
            [
                'phone' => '4561237890',
                'is_primary' => false,
                'enable_whatsapp' => false,
            ],
            [
                'phone' => '7891234560',
                'is_primary' => false,
                'enable_whatsapp' => true,
            ],
        ]);
    }
}
