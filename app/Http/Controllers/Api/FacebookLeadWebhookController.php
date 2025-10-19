<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Contact;
use App\Models\FacebookFormMapping;
use App\Models\FacebookFormFieldMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FacebookLeadWebhookController extends Controller
{
    /**
     * Verify webhook subscription (GET request)
     */
    public function verify(Request $request)
    {
        try {
            Log::info('Facebook webhook verification attempt', [
                'hub_mode' => $request->hub_mode,
                'hub_verify_token' => $request->hub_verify_token,
                'hub_challenge' => $request->hub_challenge,
                'expected_token' => config('services.facebook.verify_token')
            ]);

            // Check if all required parameters are present
            // if (!$request->has(['hub_mode', 'hub_verify_token', 'hub_challenge'])) {
            //     Log::warning('Facebook webhook verification failed: Missing required parameters');
            //     return response('Missing required parameters', 400);
            // }

            // Verify the token and mode
            if ($request->hub_mode === 'subscribe' &&
                $request->hub_verify_token === config('services.facebook.verify_token')) {
                
                Log::info('Facebook webhook verification successful');
                return response($request->hub_challenge, 200);
            }

            Log::warning('Facebook webhook verification failed: Invalid token or mode', [
                'hub_mode' => $request->hub_mode,
                'token_match' => $request->hub_verify_token === config('services.facebook.verify_token')
            ]);

            return response('Invalid verify token', 403);

        } catch (\Exception $e) {
            Log::error('Facebook webhook verification error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response('Internal server error', 500);
        }
    }

    /**
     * Handle incoming lead notifications (POST request)
     */
    public function handle(Request $request)
    {
        try {
            // Verify signature for security
            $signature = $request->header('X-Hub-Signature-256');
            if (!$this->isValidSignature($signature, $request->getContent())) {
                Log::warning('Facebook webhook: Invalid signature');
                return response('Invalid signature', 403);
            }

            $body = $request->all();
            Log::info('Facebook webhook received', ['body' => $body]);

            if (!isset($body['entry'])) {
                return response('ok', 200);
            }

            foreach ($body['entry'] as $entry) {
                foreach (($entry['changes'] ?? []) as $change) {
                    if (($change['field'] ?? '') === 'leadgen') {
                        $leadId = $change['value']['leadgen_id'] ?? null;
                        if (!$leadId) continue;

                        $this->processLead($leadId);
                    }
                }
            }

            return response('ok', 200);

        } catch (\Exception $e) {
            Log::error('Facebook webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response('Internal server error', 500);
        }
    }

    /**
     * Process a single lead
     */
    private function processLead(string $leadId)
    {
        try {
            DB::beginTransaction();

            // Fetch full lead data from Facebook
            $leadData = $this->fetchLeadFromFacebook($leadId);
            if (!$leadData) {
                DB::rollBack();
                return;
            }

            $formId = $leadData['form_id'] ?? null;
            if (!$formId) {
                Log::warning('Facebook lead missing form_id', ['lead_id' => $leadId]);
                DB::rollBack();
                return;
            }

            // Get form mapping
            $formMapping = FacebookFormMapping::where('facebook_form_id', $formId)->first();
            if (!$formMapping) {
                Log::warning('No mapping found for Facebook form', [
                    'form_id' => $formId,
                    'lead_id' => $leadId
                ]);
                DB::rollBack();
                return;
            }

            // Get field mappings for this form
            $fieldMappings = FacebookFormFieldMapping::where('form_id', $formMapping->id)->get();
            if ($fieldMappings->isEmpty()) {
                Log::warning('No field mappings found for form', [
                    'form_id' => $formMapping->id,
                    'facebook_form_id' => $formId
                ]);
                DB::rollBack();
                return;
            }

            // Map Facebook fields to contact data
            $contactData = $this->mapLeadToContactData($leadData, $fieldMappings);
            
            // Validate required fields
            $validation = $this->validateContactData($contactData, $fieldMappings);
            if (!$validation['valid']) {
                Log::warning('Contact validation failed', [
                    'lead_id' => $leadId,
                    'errors' => $validation['errors']
                ]);
                DB::rollBack();
                return;
            }

            // Create or update contact
            $contact = $this->createOrUpdateContact($contactData, $leadId, $formMapping);

            // Update contacts count
            $formMapping->incrementContactsCount();

            DB::commit();

            Log::info('Facebook lead processed successfully', [
                'lead_id' => $leadId,
                'contact_id' => $contact->id,
                'form_id' => $formId
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing Facebook lead', [
                'lead_id' => $leadId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Fetch lead data from Facebook Graph API
     */
    private function fetchLeadFromFacebook(string $leadId): ?array
    {
        $token = config('services.facebook.page_access_token');
        if (!$token) {
            Log::error('Facebook page access token not configured');
            return null;
        }

        $response = Http::get("https://graph.facebook.com/v20.0/{$leadId}", [
            'access_token' => $token,
            'fields' => 'created_time,field_data,ad_id,form_id,platform'
        ]);

        if ($response->failed()) {
            Log::error('Failed to fetch Facebook lead', [
                'lead_id' => $leadId,
                'error' => $response->json()
            ]);
            return null;
        }

        return $response->json();
    }

    /**
     * Map Facebook lead data to contact data using field mappings
     */
    private function mapLeadToContactData(array $leadData, $fieldMappings): array
    {
        // Convert Facebook field_data to key-value pairs
        $facebookFields = collect($leadData['field_data'] ?? [])
            ->pluck('values', 'name')
            ->map(function ($values) {
                return is_array($values) ? $values[0] : $values;
            });

        $contactData = [
            'source_id' => $this->getSourceId('facebook'),
            'campaign_name' => 'Facebook Lead Ads',
            'contact_method' => 'facebook_form',
            'notes' => "Facebook Lead ID: {$leadData['id']}",
            'created_at' => $leadData['created_time'] ?? now(),
        ];

        // Map each field using the mapping configuration
        foreach ($fieldMappings as $mapping) {
            $facebookValue = $facebookFields->get($mapping->facebook_field_key);
            
            if ($facebookValue !== null) {
                $contactData[$mapping->contact_column] = $facebookValue;
            }
        }

        return $contactData;
    }

    /**
     * Validate contact data against required fields
     */
    private function validateContactData(array $contactData, $fieldMappings): array
    {
        $errors = [];
        
        // Check required fields
        $requiredMappings = $fieldMappings->where('is_required', true);
        foreach ($requiredMappings as $mapping) {
            $fieldName = $mapping->contact_column;
            $value = $contactData[$fieldName] ?? null;
            
            if (empty($value)) {
                $errors[] = "Required field '{$fieldName}' is missing or empty";
            }
        }

        // Additional validation for specific fields
        if (isset($contactData['email']) && !filter_var($contactData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }

        // Handle phone field - convert to contact_phones array format if needed
        if (isset($contactData['phone']) && !empty($contactData['phone'])) {
            $contactData['contact_phones'] = [
                ['phone' => $contactData['phone'], 'type' => 'mobile']
            ];
            unset($contactData['phone']); // Remove the single phone field
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $contactData
        ];
    }

    /**
     * Create or update contact
     */
    private function createOrUpdateContact(array $contactData, string $leadId, FacebookFormMapping $formMapping): Contact
    {
        // Use lead ID as unique identifier to prevent duplicates
        $contactData['external_lead_id'] = $leadId;

        return Contact::updateOrCreate(
            ['external_lead_id' => $leadId],
            $contactData
        );
    }

    /**
     * Get source ID for Facebook
     */
    private function getSourceId(string $sourceName): ?int
    {
        // You might want to create a Source model or use a different approach
        // For now, return null or implement based on your source management
        return null;
    }

    /**
     * Verify webhook signature
     */
    private function isValidSignature(?string $sigHeader, string $raw): bool
    {
        if (!$sigHeader) {
            return false;
        }

        $appSecret = config('services.facebook.app_secret');
        if (!$appSecret) {
            Log::error('Facebook app secret not configured');
            return false;
        }

        // Header format: sha256=HEX
        [$algo, $hash] = explode('=', $sigHeader, 2) + [null, null];
        $expected = hash_hmac('sha256', $raw, $appSecret);
        
        return hash_equals($expected, $hash);
    }
}
