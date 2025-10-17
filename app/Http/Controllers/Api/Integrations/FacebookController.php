<?php

namespace App\Http\Controllers\Api\Integrations;

use App\Http\Controllers\Controller;
use App\Models\FacebookFormFieldMapping;
use App\Models\Tenant\Integration;
use App\Models\FacebookFormMapping;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookController extends Controller
{
    /**
     * Get Facebook Business Accounts (Business Manager)
     * This should be called FIRST to get the business accounts
     */
    public function getBusinessAccounts(Request $request)
    {
        $integration = Integration::where('name', 'Meta (Facebook)')->first();

        if (!$integration || !$integration->access_token) {
            return apiResponse(message: 'No Facebook access token found', code: 404);
        }

        if ($integration->isTokenExpired()) {
            return apiResponse(message: 'Facebook token expired', code: 401);
        }

        // Get user's business accounts
        $response = Http::get('https://graph.facebook.com/v20.0/me/businesses', [
            'access_token' => $integration->access_token,
            'fields' => 'id,name,verification_status,profile_picture_uri,primary_page,timezone_offset_hours_utc,created_time,updated_time'
        ]);

        if ($response->successful()) {
            $businessData = $response->json();
            $data = [
                'business_accounts' => $businessData['data'] ?? [],
                'total_accounts' => count($businessData['data'] ?? []),
                'integration_id' => $integration->id
            ];
            return apiResponse($data, 'Business accounts fetched successfully');
        }

        // Handle error
        $errorData = $response->json();
        return apiResponse(message: 'Failed to fetch business accounts. Make sure you have business_management permission', code: 400);
    }

    /**
     * Get Ad Accounts from a specific Business Account
     * Call this AFTER getting business accounts
     */
    public function getAdAccountsFromBusiness(Request $request)
    {
        $request->validate([
            'business_id' => 'required|string'
        ]);

        $integration = Integration::where('name', 'Meta (Facebook)')->first();

        if (!$integration || !$integration->access_token) {
            return apiResponse(message: 'No Facebook access token found', code: 404);
        }

        if ($integration->isTokenExpired()) {
            return apiResponse(message: 'Facebook token expired', code: 401);
        }

        // Get ad accounts from business
        $response = Http::get("https://graph.facebook.com/v20.0/{$request->business_id}/owned_ad_accounts", [
            'access_token' => $integration->access_token,
            'fields' => 'id,name,account_id,account_status,currency,timezone_name,business_name,amount_spent,balance,owner'
        ]);

        if ($response->successful()) {
            $adAccountsData = $response->json();
            $data = [
                'business_id' => $request->business_id,
                'ad_accounts' => $adAccountsData['data'] ?? [],
                'total_accounts' => count($adAccountsData['data'] ?? []),
                'integration_id' => $integration->id
            ];
            return apiResponse($data, 'Ad accounts fetched successfully');
        }

        $errorData = $response->json();
        return apiResponse(message: 'Failed to fetch ad accounts from business', code: 400);
    }

    /**
     * Get integration status
     */
    public function getStatus()
    {
        $integration = Integration::where('name', 'Meta (Facebook)')->first();

        if (!$integration) {
            return apiResponse(message: 'Meta (Facebook) integration not found', code: 404);
        }

        $data = [
            'is_connected' => $integration->isConnected(),
            'has_valid_credentials' => $integration->hasValidCredentials(),
            'is_token_expired' => $integration->isTokenExpired(),
            'token_expires_at' => $integration->token_expires_at,
            'token_expires_in' => $integration->token_expires_in,
        ];
        return apiResponse($data, 'Integration status retrieved successfully');
    }

    /**
     * Save Facebook access token from client
     */
    public function saveToken(Request $request)
    {
        $request->validate([
            'access_token' => 'required|string',
            'user_id' => 'required|string',
            'expires_in' => 'required|integer',
        ]);

        $integration = Integration::where('name', 'Meta (Facebook)')->first();

        if (!$integration) {
            return apiResponse(message: 'Meta (Facebook) integration not found', code: 404);
        }

        $integration->update([
            'access_token' => $request->access_token,
            'token_expires_at' => now()->addSeconds($request->expires_in)
        ]);

        return apiResponse($integration->fresh(), 'Token saved successfully');
    }

    /**
     * Get forms from selected page
     * Step 4: Get Forms from specific Page
     */
    public function getFormsFromPage(Request $request)
    {
        $request->validate([
            'page_id' => 'required|string'
        ]);

        $integration = Integration::where('name', 'Meta (Facebook)')->first();

        if (!$integration || !$integration->access_token) {
            return apiResponse(message: 'No Facebook access token found', code: 404);
        }

        if ($integration->isTokenExpired()) {
            return apiResponse(message: 'Facebook token expired', code: 401);
        }

        // First, get the page access token
        $pageTokenResponse = Http::get("https://graph.facebook.com/v20.0/{$request->page_id}", [
            'access_token' => $integration->access_token,
            'fields' => 'access_token'
        ]);

        if (!$pageTokenResponse->successful()) {
            return apiResponse(message: 'Failed to get page access token', code: 400);
        }

        $pageData = $pageTokenResponse->json();
        $pageAccessToken = $pageData['access_token'] ?? null;

        if (!$pageAccessToken) {
            return apiResponse(message: 'Page access token not available', code: 400);
        }

        // Get forms from page using page access token
        $response = Http::get("https://graph.facebook.com/v20.0/{$request->page_id}/leadgen_forms", [
            'access_token' => $pageAccessToken,
            'fields' => 'id,name,status,leads_count,created_time,updated_time,privacy_policy_url,legal_content_url'
        ]);

        if ($response->successful()) {
            $formsData = $response->json();
            $data = [
                'forms' => $formsData['data'] ?? [],
                'total_forms' => count($formsData['data'] ?? []),
                'integration_id' => $integration->id
            ];
            return apiResponse($data, 'Forms fetched successfully');
        }

        $errorData = $response->json();
        return apiResponse(message: $errorData, code: 400);
    }


    /**
     * Get contacts table columns for field mapping
     * Returns only the fields used in the regular contacts store API
     */
    public function getContactsColumns(Request $request)
    {
        try {
            // Define the exact fields used in ContactRequest with their validation rules
            $contactFields = [
                // Basic Info (Required)
                'first_name' => [
                    'column_name' => 'first_name',
                    'is_required' => true,
                    'validation' => 'required|string|max:255',
                    'description' => 'First name of the contact'
                ],
                'last_name' => [
                    'column_name' => 'last_name',
                    'is_required' => false,
                    'validation' => 'nullable|string|max:255',
                    'description' => 'Last name of the contact'
                ],
                'email' => [
                    'column_name' => 'email',
                    'is_required' => true,
                    'validation' => 'required|email|unique:contacts,email',
                    'description' => 'Email address (must be unique)'
                ],

                // Contact Phones (Required - array)
                'contact_phones' => [
                    'column_name' => 'contact_phones',
                    'is_required' => true,
                    'validation' => 'required|array|min:1',
                    'description' => 'Array of phone numbers (at least one required)',
                    'is_array' => true,
                    'array_fields' => [
                        'phone' => 'required|string|max:20',
                        'is_primary' => 'sometimes|boolean',
                        'enable_whatsapp' => 'sometimes|boolean'
                    ]
                ],

                // Job Info
                'job_title' => [
                    'column_name' => 'job_title',
                    'is_required' => false,
                    'validation' => 'nullable|string|max:255',
                    'description' => 'Job title'
                ],
                'department' => [
                    'column_name' => 'department',
                    'is_required' => false,
                    'validation' => 'nullable|string|max:255',
                    'description' => 'Department'
                ],

                // Status & Source
                'status' => [
                    'column_name' => 'status',
                    'is_required' => false,
                    'validation' => 'nullable|enum:active,inactive,unqualified',
                    'description' => 'Contact status',
                    'options' => ['active', 'inactive', 'unqualified']
                ],
                'source_id' => [
                    'column_name' => 'source_id',
                    'is_required' => false,
                    'validation' => 'nullable|exists:sources,id',
                    'description' => 'Source ID (must exist in sources table)'
                ],
                'campaign_name' => [
                    'column_name' => 'campaign_name',
                    'is_required' => false,
                    'validation' => 'nullable|string|max:255',
                    'description' => 'Campaign name'
                ],

                // Communication Preferences
                'contact_method' => [
                    'column_name' => 'contact_method',
                    'is_required' => false,
                    'validation' => 'nullable|enum:email,phone,sms,whatsapp',
                    'description' => 'Preferred contact method',
                    'options' => ['email', 'phone', 'sms', 'whatsapp']
                ],
                'email_permission' => [
                    'column_name' => 'email_permission',
                    'is_required' => false,
                    'validation' => 'nullable|boolean',
                    'description' => 'Email communication permission'
                ],
                'phone_permission' => [
                    'column_name' => 'phone_permission',
                    'is_required' => false,
                    'validation' => 'nullable|boolean',
                    'description' => 'Phone communication permission'
                ],
                'whatsapp_permission' => [
                    'column_name' => 'whatsapp_permission',
                    'is_required' => false,
                    'validation' => 'nullable|boolean',
                    'description' => 'WhatsApp communication permission'
                ],

                // Company Info
                'company_name' => [
                    'column_name' => 'company_name',
                    'is_required' => false,
                    'validation' => 'nullable|string|max:255',
                    'description' => 'Company name'
                ],
                'website' => [
                    'column_name' => 'website',
                    'is_required' => false,
                    'validation' => 'nullable|string|max:255',
                    'description' => 'Company website'
                ],
                'industry' => [
                    'column_name' => 'industry',
                    'is_required' => false,
                    'validation' => 'nullable|enum:Technology,Healthcare,Finance,Manufacturing,Retail,Education,Other',
                    'description' => 'Industry type',
                    'options' => ['Technology', 'Healthcare', 'Finance', 'Manufacturing', 'Retail', 'Education', 'Other']
                ],
                'company_size' => [
                    'column_name' => 'company_size',
                    'is_required' => false,
                    'validation' => 'nullable|enum:1-10 employees,11-50 employees,51-200 employees,201-500 employees,500+ employees',
                    'description' => 'Company size',
                    'options' => ['1-10 employees', '11-50 employees', '51-200 employees', '201-500 employees', '500+ employees']
                ],

                // Address Info
                'address' => [
                    'column_name' => 'address',
                    'is_required' => false,
                    'validation' => 'nullable|string|max:255',
                    'description' => 'Street address'
                ],
                'country_id' => [
                    'column_name' => 'country_id',
                    'is_required' => false,
                    'validation' => 'nullable|exists:countries,id',
                    'description' => 'Country ID (must exist in countries table)'
                ],
                'city_id' => [
                    'column_name' => 'city_id',
                    'is_required' => false,
                    'validation' => 'nullable|exists:cities,id',
                    'description' => 'City ID (must exist in cities table)'
                ],
                'state' => [
                    'column_name' => 'state',
                    'is_required' => false,
                    'validation' => 'nullable|string|max:255',
                    'description' => 'State/Province'
                ],
                'zip_code' => [
                    'column_name' => 'zip_code',
                    'is_required' => false,
                    'validation' => 'nullable|string|max:255',
                    'description' => 'ZIP/Postal code'
                ],

                // System Fields
                'user_id' => [
                    'column_name' => 'user_id',
                    'is_required' => false,
                    'validation' => 'nullable|exists:users,id',
                    'description' => 'Assigned user ID (must exist in users table)'
                ],

                // Additional Fields
                'tags' => [
                    'column_name' => 'tags',
                    'is_required' => false,
                    'validation' => 'nullable|array',
                    'description' => 'Array of tags',
                    'is_array' => true
                ],
                'notes' => [
                    'column_name' => 'notes',
                    'is_required' => false,
                    'validation' => 'nullable|string|max:255',
                    'description' => 'Additional notes'
                ]
            ];

            $data = [
                'columns' => array_values($contactFields),
                'total_columns' => count($contactFields),
                'required_columns' => array_values(array_filter($contactFields, fn($col) => $col['is_required'])),
                'array_fields' => array_values(array_filter($contactFields, fn($col) => isset($col['is_array']) && $col['is_array']))
            ];

            return apiResponse($data, 'Contacts API fields retrieved successfully');
        } catch (\Exception $e) {
            return apiResponse(message: 'Failed to get contacts columns: ' . $e->getMessage(), code: 500);
        }
    }

    /**
     * Save Facebook form field mapping
     */
    public function saveFormFieldMapping(Request $request)
    {
        DB::beginTransaction();
        $request->validate([
            'facebook_form_id' => 'required|string',
            'form_name' => 'required|string|max:255',
            'mappings' => 'required|array',
            'mappings.*.facebook_field_key' => 'required|string',
            'mappings.*.contact_column' => 'required|string',
        ]);

        try {
            // Define the exact fields used in ContactRequest
            $contactFields = [
                'first_name',
                'last_name',
                'email',
                'contact_phones',
                'job_title',
                'department',
                'status',
                'source_id',
                'campaign_name',
                'contact_method',
                'email_permission',
                'phone_permission',
                'whatsapp_permission',
                'company_name',
                'website',
                'industry',
                'company_size',
                'address',
                'country_id',
                'city_id',
                'state',
                'zip_code',
                'user_id',
                'tags',
                'notes'
            ];

            $requiredFields = ['first_name', 'email'];

            // Validate mappings
            $mappedColumns = [];
            $errors = [];

            foreach ($request->mappings as $index => $mapping) {
                // Check if contact column exists
                if (!in_array($mapping['contact_column'], $contactFields)) {
                    $errors[] = "Invalid contact column: {$mapping['contact_column']}";
                    continue;
                }

                // Check for duplicate mappings
                if (in_array($mapping['contact_column'], $mappedColumns)) {
                    $errors[] = "Duplicate mapping for column: {$mapping['contact_column']}";
                    continue;
                }

                $mappedColumns[] = $mapping['contact_column'];
            }

            // Check if all required columns are mapped
            $unmappedRequired = array_diff($requiredFields, $mappedColumns);
            if (!empty($unmappedRequired)) {
                $errors[] = "Required columns not mapped: " . implode(', ', $unmappedRequired);
            }

            if (!empty($errors)) {
                return apiResponse(message: 'Mapping validation failed: ' . implode('; ', $errors), code: 400);
            }

            // Save mapping using the model (this will save to both tables)
            $form = FacebookFormMapping::create(
                [
                    'facebook_form_id' => $request->facebook_form_id,
                    'form_name' => $request->form_name
                ]
            );
            foreach ($request->mappings as $mapping) {
                FacebookFormFieldMapping::create([
                    'form_id' => $form->id,
                    'facebook_field_key' => $mapping['facebook_field_key'],
                    'contact_column' => $mapping['contact_column']
                ]);
            }
            DB::commit();

            return apiResponse(message: 'Form field mapping saved successfully');
        } catch (\Exception $e) {
            return apiResponse(message: 'Failed to save mapping: ' . $e->getMessage(), code: 500);
        }
    }

    /**
     * Get saved form field mapping
     */
    public function getFormFieldMapping(Request $request)
    {
        $request->validate([
            'form_id' => 'required|string'
        ]);

        try {
            $mapping = FacebookFormMapping::findByFormId($request->form_id);

            if (!$mapping) {
                return apiResponse(message: 'No mapping found for this form', code: 404);
            }

            $data = [
                'form_id' => $mapping->getFormId(),
                'form_name' => $mapping->getFormName(),
                'mappings' => $mapping->getMappings(),
                'total_contacts_count' => $mapping->getTotalContactsCount(),
                'created_at' => $mapping->created_at,
                'updated_at' => $mapping->updated_at
            ];

            return apiResponse($data, 'Form field mapping retrieved successfully');
        } catch (\Exception $e) {
            return apiResponse(message: 'Failed to get mapping: ' . $e->getMessage(), code: 500);
        }
    }

    /**
     * Update contacts count for a form mapping
     */
    public function updateContactsCount(Request $request)
    {
        $request->validate([
            'form_id' => 'required|string',
            'contacts_count' => 'required|integer|min:0'
        ]);

        try {
            $mapping = FacebookFormMapping::findByFormId($request->form_id);

            if (!$mapping) {
                return apiResponse(message: 'No mapping found for this form', code: 404);
            }

            $mapping->setTotalContactsCount($request->contacts_count);
            $mapping->save();

            $data = [
                'form_id' => $mapping->getFormId(),
                'form_name' => $mapping->getFormName(),
                'total_contacts_count' => $mapping->getTotalContactsCount(),
                'updated_at' => $mapping->updated_at
            ];

            return apiResponse($data, 'Contacts count updated successfully');
        } catch (\Exception $e) {
            return apiResponse(message: 'Failed to update contacts count: ' . $e->getMessage(), code: 500);
        }
    }

    /**
     * Increment contacts count for a form mapping
     */
    public function incrementContactsCount(Request $request)
    {
        $request->validate([
            'form_id' => 'required|string',
            'increment' => 'integer|min:1'
        ]);

        try {
            $mapping = FacebookFormMapping::findByFormId($request->form_id);

            if (!$mapping) {
                return apiResponse(message: 'No mapping found for this form', code: 404);
            }

            $increment = $request->get('increment', 1);
            $mapping->incrementContactsCount($increment);
            $mapping->save();

            $data = [
                'form_id' => $mapping->getFormId(),
                'form_name' => $mapping->getFormName(),
                'total_contacts_count' => $mapping->getTotalContactsCount(),
                'incremented_by' => $increment,
                'updated_at' => $mapping->updated_at
            ];

            return apiResponse($data, 'Contacts count incremented successfully');
        } catch (\Exception $e) {
            return apiResponse(message: 'Failed to increment contacts count: ' . $e->getMessage(), code: 500);
        }
    }

    /**
     * Get all form mappings with their details
     */
    public function getAllFormMappings(Request $request)
    {
        try {
            $mappings = FacebookFormMapping::orderBy('created_at', 'desc')->get();

            $data = $mappings->map(function ($mapping) {
                return [
                    'form_id' => $mapping->getFormId(),
                    'form_name' => $mapping->getFormName(),
                    'total_contacts_count' => $mapping->getTotalContactsCount(),
                    'total_mappings' => count($mapping->getMappings()),
                    'created_at' => $mapping->created_at,
                    'updated_at' => $mapping->updated_at
                ];
            });

            return apiResponse([
                'mappings' => $data,
                'total_forms' => $mappings->count(),
                'total_contacts' => $mappings->sum('total_contacts_count')
            ], 'All form mappings retrieved successfully');
        } catch (\Exception $e) {
            return apiResponse(message: 'Failed to get form mappings: ' . $e->getMessage(), code: 500);
        }
    }

    /**
     * Test mapping validation with sample data
     */
    public function testMappingValidation(Request $request)
    {
        $request->validate([
            'form_id' => 'required|string',
            'sample_mappings' => 'required|array'
        ]);

        try {
            // Define required fields
            $requiredFields = ['first_name', 'email', 'contact_phones'];

            // Check if all required fields are mapped
            $mappedFields = array_column($request->sample_mappings, 'contact_column');
            $unmappedRequired = array_diff($requiredFields, $mappedFields);

            $validation = [
                'form_id' => $request->form_id,
                'mapped_fields' => $mappedFields,
                'required_fields' => $requiredFields,
                'unmapped_required' => $unmappedRequired,
                'validation_passed' => empty($unmappedRequired),
                'message' => empty($unmappedRequired)
                    ? 'All required fields are mapped'
                    : 'Missing required fields: ' . implode(', ', $unmappedRequired)
            ];

            return apiResponse($validation, $validation['message']);
        } catch (\Exception $e) {
            return apiResponse(message: 'Validation test failed: ' . $e->getMessage(), code: 500);
        }
    }

    /**
     * Get form fields from selected form
     * Step 5: Get Form Fields from specific Form
     */
    public function getFormFields(Request $request)
    {
        $request->validate([
            'form_id' => 'required|string'
        ]);

        $integration = Integration::where('name', 'Meta (Facebook)')->first();

        if (!$integration || !$integration->access_token) {
            return apiResponse(message: 'No Facebook access token found', code: 404);
        }

        if ($integration->isTokenExpired()) {
            return apiResponse(message: 'Facebook token expired', code: 401);
        }

        // Get form fields - using only confirmed supported fields
        $response = Http::get("https://graph.facebook.com/v20.0/{$request->form_id}", [
            'access_token' => $integration->access_token,
            'fields' => 'id,name,status,leads_count,questions'
        ]);

        if ($response->successful()) {
            $formData = $response->json();

            // Extract questions/fields from the form
            $questions = $formData['questions'] ?? [];
            $fields = [];

            foreach ($questions as $question) {
                $field = [
                    'id' => $question['id'] ?? null,
                    'key' => $question['key'] ?? null,
                    'label' => $question['label'] ?? null,
                    'type' => $question['type'] ?? null,
                    'options' => $question['options'] ?? null,
                    'required' => $question['required'] ?? false,
                    'validation' => $question['validation'] ?? null
                ];
                $fields[] = $field;
            }

            $data = [
                'form_name' => $formData['name'] ?? null,
                'form_status' => $formData['status'] ?? null,
                'leads_count' => $formData['leads_count'] ?? 0,
                'fields' => $fields,
                'total_fields' => count($fields),
                'integration_id' => $integration->id
            ];
            return apiResponse($data, 'Form fields fetched successfully');
        }

        $errorData = $response->json();
        return apiResponse(message: 'Failed to fetch form fields: ' . ($errorData['error']['message'] ?? 'Unknown error'), code: 400);
    }

    /**
     * Get leads from selected form
     */
    public function getLeadsFromForm(Request $request)
    {
        $request->validate([
            'form_id' => 'required|string',
            'limit' => 'integer|min:1|max:100'
        ]);

        $integration = Integration::where('name', 'Meta (Facebook)')->first();

        if (!$integration || !$integration->access_token) {
            return apiResponse(message: 'No Facebook access token found', code: 404);
        }

        if ($integration->isTokenExpired()) {
            return apiResponse(message: 'Facebook token expired', code: 401);
        }

        // Get leads from form
        $response = Http::get("https://graph.facebook.com/v20.0/{$request->form_id}/leads", [
            'access_token' => $integration->access_token,
            'fields' => 'id,created_time,field_data',
            'limit' => $request->get('limit', 25)
        ]);

        if ($response->successful()) {
            $leadsData = $response->json();
            $data = [
                'form_id' => $request->form_id,
                'leads' => $leadsData['data'] ?? [],
                'total_leads' => count($leadsData['data'] ?? []),
                'integration_id' => $integration->id
            ];
            return apiResponse($data, 'Leads fetched successfully');
        }

        $errorData = $response->json();
        return apiResponse(message: 'Failed to fetch leads from form', code: 400);
    }

    /**
     * Get Facebook OAuth authorization URL with proper permissions
     */
    public function getAuthUrl(Request $request)
    {
        $redirectUri = $this->getTenantRedirectUri();
        $configId = env('FACEBOOK_CONFIG_ID', '1699506300921951');

        // Required permissions for Business Manager, Ad Accounts, and Lead Ads
        $scope = implode(',', [
            'public_profile',
            'email',
            'business_management',      // Access to Business Manager
            'ads_management',            // Manage ad accounts
            'ads_read',                  // Read ad account data
            'leads_retrieval',           // Access lead forms and leads
            'pages_show_list',           // List pages
            'pages_read_engagement',     // Read page engagement
            'pages_manage_ads',          // Manage page ads and lead forms
            'pages_read_user_content',   // Read page content
        ]);

        // Use Facebook Login for Business with config_id
        $authUrl = 'https://www.facebook.com/v20.0/dialog/oauth?' . http_build_query([
            'client_id' => env('FACEBOOK_CLIENT_ID'),
            'redirect_uri' => $redirectUri,
            'config_id' => $configId,
            'response_type' => 'code',
            'scope' => $scope,
            'state' => $request->get('state', bin2hex(random_bytes(16)))
        ]);

        $data = [
            'auth_url' => $authUrl,
            'redirect_uri' => $redirectUri,
            'config_id' => $configId,
            'permissions' => explode(',', $scope),
            'app_type' => 'business',
            'login_type' => 'facebook_login_for_business'
        ];
        return apiResponse($data, 'Authorization URL generated successfully');
    }

    /**
     * Handle Facebook OAuth callback
     */
    public function handleCallback(Request $request)
    {
        $code = $request->get('code');
        $error = $request->get('error');

        if ($error) {
            return apiResponse(message: 'Facebook authorization failed: ' . $request->get('error_description', 'Unknown error'), code: 400);
        }

        if (!$code) {
            return apiResponse(message: 'Authorization code not provided', code: 400);
        }

        // Exchange code for access token
        $redirectUri = $this->getTenantRedirectUri();

        $response = Http::get('https://graph.facebook.com/v20.0/oauth/access_token', [
            'client_id' => env('FACEBOOK_CLIENT_ID'),
            'redirect_uri' => $redirectUri,
            'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
            'code' => $code,
        ]);

        $data = $response->json();

        if (isset($data['error'])) {
            return apiResponse(message: 'Facebook OAuth error: ' . $data['error'], code: 400);
        }

        // Store access token
        $accessToken = $data['access_token'];
        $expiresIn = $data['expires_in'];

        // Save to database
        $integration = Integration::where('name', 'Meta (Facebook)')->first();
        if ($integration) {
            $integration->update([
                'access_token' => $accessToken,
                'token_expires_at' => now()->addSeconds($expiresIn)
            ]);
        }

        // Get user information
        $userResponse = Http::get('https://graph.facebook.com/v20.0/me', [
            'access_token' => $accessToken,
            'fields' => 'id,name,email',
        ]);

        $user = $userResponse->json();

        $data = [
            'access_token' => $accessToken,
            'expires_in' => $expiresIn,
            'user' => $user,
            'integration' => $integration->fresh()
        ];
        return apiResponse($data, 'Facebook OAuth successful');
    }

    /**
     * Validate Facebook access token
     */
    public function validateFacebookToken(Request $request)
    {
        $integration = Integration::where('name', 'Meta (Facebook)')->first();

        if (!$integration || !$integration->access_token) {
            return apiResponse(message: 'No Facebook access token found', code: 404);
        }

        if ($integration->isTokenExpired()) {
            return apiResponse(message: 'Facebook token expired', code: 401);
        }

        // Validate token with Facebook
        $response = Http::get('https://graph.facebook.com/v20.0/me', [
            'access_token' => $integration->access_token,
            'fields' => 'id,name'
        ]);

        if ($response->successful()) {
            $userData = $response->json();
            $data = [
                'valid' => true,
                'user_id' => $userData['id'] ?? null,
                'user_name' => $userData['name'] ?? null,
                'integration_id' => $integration->id,
                'token_expires_at' => $integration->token_expires_at
            ];
            return apiResponse($data, 'Facebook access token is valid');
        }

        $errorData = $response->json();
        return apiResponse(message: 'Invalid Facebook access token', code: 400);
    }

    /**
     * Get Facebook app configuration for client-side integration
     */
    public function getAppConfig()
    {
        $configId = env('FACEBOOK_CONFIG_ID', '1699506300921951');

        $data = [
            'app_id' => env('FACEBOOK_CLIENT_ID'),
            'version' => 'v20.0',
            'redirect_uri' => $this->getTenantRedirectUri(),
            'config_id' => $configId,
            'app_type' => 'business',
            'login_type' => 'facebook_login_for_business'
        ];
        return apiResponse($data, 'App configuration retrieved successfully');
    }

    /**
     * Revoke access token
     */
    public function revokeToken(Request $request)
    {
        $integration = Integration::where('name', 'Meta (Facebook)')->first();

        if (!$integration || !$integration->access_token) {
            return apiResponse(message: 'No access token found', code: 404);
        }

        // Revoke token with Facebook
        $response = Http::delete('https://graph.facebook.com/v20.0/me/permissions', [
            'access_token' => $integration->access_token
        ]);

        // Clear token from database
        $integration->update([
            'access_token' => null,
            'refresh_token' => null,
            'token_expires_at' => null
        ]);

        $data = [
            'facebook_response' => $response->json()
        ];
        return apiResponse($data, 'Token revoked successfully');
    }

    /**
     * Get tenant-specific redirect URI
     */
    private function getTenantRedirectUri(): string
    {
        $tenant = tenant();
        $baseUrl = config('app.url');

        if (app()->environment('production')) {
            $baseUrl = str_replace('http://', 'https://', $baseUrl);
        }

        if ($tenant) {
            $subdomain = $tenant->id;
            $baseUrl = str_replace('://', "://{$subdomain}.", $baseUrl);
        }

        return $baseUrl . '/api/facebook/callback';
    }

    /**
     * Data Deletion Callback - Required by Facebook for GDPR compliance
     */
    public function dataDeletionCallback(Request $request)
    {
        $signedRequest = $request->get('signed_request');

        if (!$signedRequest) {
            return apiResponse(message: 'Missing signed_request', code: 400);
        }

        $data = $this->parseSignedRequest($signedRequest);

        if (!$data) {
            return apiResponse(message: 'Invalid signed_request', code: 400);
        }

        $userId = $data['user_id'] ?? null;

        if (!$userId) {
            return apiResponse(message: 'Missing user_id', code: 400);
        }

        Log::info('Facebook data deletion request', [
            'user_id' => $userId,
            'request_data' => $data,
            'timestamp' => now()
        ]);

        $data = [
            'url' => route('facebook.data-deletion-confirmation', ['user_id' => $userId]),
            'confirmation_code' => 'DELETE_' . $userId . '_' . time()
        ];
        return apiResponse($data, 'Data deletion callback processed successfully');
    }

    /**
     * Parse Facebook signed request
     */
    private function parseSignedRequest($signedRequest)
    {
        list($encodedSig, $payload) = explode('.', $signedRequest, 2);

        $secret = env('FACEBOOK_CLIENT_SECRET');
        $expectedSig = hash_hmac('sha256', $payload, $secret, true);
        $sig = base64_decode(strtr($encodedSig, '-_', '+/'));

        if (hash_equals($expectedSig, $sig)) {
            return json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
        }

        return null;
    }




    /**
     * Alternative method to get pages by exploring campaigns and adsets
     */
    public function getPagesFromAdAccountAlternative(Request $request)
    {
        $request->validate([
            'ad_account_id' => 'required|string'
        ]);

        $integration = Integration::where('name', 'Meta (Facebook)')->first();

        if (!$integration || !$integration->access_token) {
            return apiResponse(message: 'No Facebook access token found', code: 404);
        }

        if ($integration->isTokenExpired()) {
            return apiResponse(message: 'Facebook token expired', code: 401);
        }

        $allPages = [];
        $pageIds = [];

        try {
            // Method 1: Get campaigns and extract page info
            $campaignsResponse = Http::get("https://graph.facebook.com/v20.0/{$request->ad_account_id}/campaigns", [
                'access_token' => $integration->access_token,
                'fields' => 'id,name,status,objective',
                'limit' => 50
            ]);

            if ($campaignsResponse->successful()) {
                $campaigns = $campaignsResponse->json()['data'] ?? [];

                foreach ($campaigns as $campaign) {
                    // Get adsets for each campaign
                    $adsetsResponse = Http::get("https://graph.facebook.com/v20.0/{$campaign['id']}/adsets", [
                        'access_token' => $integration->access_token,
                        'fields' => 'id,name,status,promoted_object',
                        'limit' => 10
                    ]);

                    if ($adsetsResponse->successful()) {
                        $adsets = $adsetsResponse->json()['data'] ?? [];

                        foreach ($adsets as $adset) {
                            if (isset($adset['promoted_object']['page_id'])) {
                                $pageId = $adset['promoted_object']['page_id'];
                                if (!in_array($pageId, $pageIds)) {
                                    $pageIds[] = $pageId;

                                    // Get page details
                                    $pageResponse = Http::get("https://graph.facebook.com/v20.0/{$pageId}", [
                                        'access_token' => $integration->access_token,
                                        'fields' => 'id,name,category'
                                    ]);

                                    if ($pageResponse->successful()) {
                                        $pageData = $pageResponse->json();
                                        $pageData['source'] = 'campaign_adset';
                                        $pageData['campaign_id'] = $campaign['id'];
                                        $pageData['adset_id'] = $adset['id'];
                                        $allPages[] = $pageData;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // Method 2: If no pages found via campaigns, try ads directly
            if (empty($allPages)) {
                $adsResponse = Http::get("https://graph.facebook.com/v20.0/{$request->ad_account_id}/ads", [
                    'access_token' => $integration->access_token,
                    'fields' => 'id,name,status,creative',
                    'limit' => 50
                ]);

                if ($adsResponse->successful()) {
                    $ads = $adsResponse->json()['data'] ?? [];

                    foreach ($ads as $ad) {
                        if (isset($ad['creative']['object_story_spec']['page_id'])) {
                            $pageId = $ad['creative']['object_story_spec']['page_id'];
                            if (!in_array($pageId, $pageIds)) {
                                $pageIds[] = $pageId;

                                // Get page details
                                $pageResponse = Http::get("https://graph.facebook.com/v20.0/{$pageId}", [
                                    'access_token' => $integration->access_token,
                                    'fields' => 'id,name,category'
                                ]);

                                if ($pageResponse->successful()) {
                                    $pageData = $pageResponse->json();
                                    $pageData['source'] = 'ad_creative';
                                    $pageData['ad_id'] = $ad['id'];
                                    $allPages[] = $pageData;
                                }
                            }
                        }
                    }
                }
            }

            $data = [
                'ad_account_id' => $request->ad_account_id,
                'pages' => $allPages,
                'total_pages' => count($allPages),
                'method_used' => empty($allPages) ? 'none' : (strpos($allPages[0]['source'] ?? '', 'campaign') !== false ? 'campaign_adset' : 'ad_creative'),
                'integration_id' => $integration->id
            ];
            return apiResponse($data, 'Pages fetched successfully using alternative method');
        } catch (\Exception $e) {
            return apiResponse(message: 'Failed to get pages from ad account: ' . $e->getMessage(), code: 400);
        }
    }

    /**
     * Helper method to get pages via campaigns
     */
    private function getPagesViaCampaigns($adAccountId, $accessToken)
    {
        $allPages = [];
        $pageIds = [];

        try {
            // Get campaigns
            $campaignsResponse = Http::get("https://graph.facebook.com/v20.0/{$adAccountId}/campaigns", [
                'access_token' => $accessToken,
                'fields' => 'id,name,status',
                'limit' => 20
            ]);

            if ($campaignsResponse->successful()) {
                $campaigns = $campaignsResponse->json()['data'] ?? [];

                foreach ($campaigns as $campaign) {
                    // Get adsets for each campaign
                    $adsetsResponse = Http::get("https://graph.facebook.com/v20.0/{$campaign['id']}/adsets", [
                        'access_token' => $accessToken,
                        'fields' => 'id,name,promoted_object',
                        'limit' => 5
                    ]);

                    if ($adsetsResponse->successful()) {
                        $adsets = $adsetsResponse->json()['data'] ?? [];

                        foreach ($adsets as $adset) {
                            if (isset($adset['promoted_object']['page_id'])) {
                                $pageId = $adset['promoted_object']['page_id'];
                                if (!in_array($pageId, $pageIds)) {
                                    $pageIds[] = $pageId;

                                    // Get page details
                                    $pageResponse = Http::get("https://graph.facebook.com/v20.0/{$pageId}", [
                                        'access_token' => $accessToken,
                                        'fields' => 'id,name,category'
                                    ]);

                                    if ($pageResponse->successful()) {
                                        $pageData = $pageResponse->json();
                                        $allPages[] = $pageData;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error in getPagesViaCampaigns', ['error' => $e->getMessage()]);
        }

        return $allPages;
    }

    /**
     * Pages From Ad Account - Get pages using me/accounts endpoint
     */
    public function pagesFromAdAccount(Request $request)
    {
        $request->validate([
            'ad_account_id' => 'required|string'
        ]);

        $integration = Integration::where('name', 'Meta (Facebook)')->first();

        if (!$integration || !$integration->access_token) {
            return apiResponse(message: 'No Facebook access token found', code: 404);
        }

        if ($integration->isTokenExpired()) {
            return apiResponse(message: 'Facebook token expired', code: 401);
        }

        try {
            // Use me/accounts endpoint to get pages
            $response = Http::get('https://graph.facebook.com/v20.0/me/accounts', [
                'access_token' => $integration->access_token,
                'fields' => 'id,name,category,tasks'
            ]);

            if ($response->successful()) {
                $pagesData = $response->json();
                $pages = $pagesData['data'] ?? [];

                $data = [
                    'pages' => $pages,
                    'total_pages' => count($pages),
                    'endpoint_used' => 'me/accounts',
                    'integration_id' => $integration->id
                ];
                return apiResponse($data, 'Pages fetched successfully using me/accounts endpoint');
            } else {
                $errorData = $response->json();
                return apiResponse(message: 'Failed to fetch pages from me/accounts endpoint', code: 400);
            }
        } catch (\Exception $e) {
            return apiResponse(message: 'Exception occurred while fetching pages: ' . $e->getMessage(), code: 400);
        }
    }

    /**
     * Check current Facebook permissions and provide guidance
     */
    public function checkPermissions(Request $request)
    {
        $integration = Integration::where('name', 'Meta (Facebook)')->first();

        if (!$integration || !$integration->access_token) {
            return apiResponse(message: 'No Facebook access token found', code: 404);
        }

        try {
            // Get current permissions
            $response = Http::get('https://graph.facebook.com/v20.0/me/permissions', [
                'access_token' => $integration->access_token
            ]);

            if (!$response->successful()) {
                return apiResponse(message: 'Failed to fetch permissions', code: 400);
            }

            $permissionsData = $response->json();
            $permissions = $permissionsData['data'] ?? [];

            // Required permissions for lead forms
            $requiredPermissions = [
                'pages_manage_ads' => 'Manage page ads and lead forms',
                'pages_show_list' => 'List pages',
                'leads_retrieval' => 'Access lead forms and leads',
                'ads_management' => 'Manage ad accounts',
                'business_management' => 'Access Business Manager'
            ];

            $grantedPermissions = [];
            $missingPermissions = [];

            foreach ($requiredPermissions as $permission => $description) {
                $found = false;
                foreach ($permissions as $perm) {
                    if ($perm['permission'] === $permission && $perm['status'] === 'granted') {
                        $grantedPermissions[] = [
                            'permission' => $permission,
                            'description' => $description,
                            'status' => 'granted'
                        ];
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $missingPermissions[] = [
                        'permission' => $permission,
                        'description' => $description,
                        'status' => 'missing'
                    ];
                }
            }

            $data = [
                'all_permissions' => $permissions,
                'granted_permissions' => $grantedPermissions,
                'missing_permissions' => $missingPermissions,
                'can_access_lead_forms' => empty($missingPermissions),
                'next_steps' => empty($missingPermissions) ?
                    ['All required permissions granted! You can now access lead forms.'] :
                    [
                        '1. Go to Facebook App Dashboard: https://developers.facebook.com/apps/',
                        '2. Select your app',
                        '3. Go to App Review > Permissions and Features',
                        '4. Request Advanced Access for missing permissions:',
                        '   - ' . implode(', ', array_column($missingPermissions, 'permission')),
                        '5. Provide detailed use case description when prompted',
                        '6. Wait for Facebook approval (can take 1-7 days)',
                        '7. Re-authenticate after approval to get new permissions'
                    ],
                'integration_id' => $integration->id
            ];
            return apiResponse($data, 'Permissions checked successfully');
        } catch (\Exception $e) {
            return apiResponse(message: 'Failed to check permissions: ' . $e->getMessage(), code: 400);
        }
    }
}
