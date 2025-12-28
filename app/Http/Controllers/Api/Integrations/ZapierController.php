<?php

namespace App\Http\Controllers\Api\Integrations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant\Integration;
use App\Enums\PlatformEnum;
use App\Enums\IntegrationStatusEnum;
use Illuminate\Support\Str;
use App\Services\ContactService;
use App\DTO\Contact\ContactDTO;
use Illuminate\Support\Facades\DB;

class ZapierController extends Controller
{
    public function __construct(protected ContactService $contactService)
    {
    }

    /**
     * Get Zapier integration settings (Webhook URL, API Key)
     */
    public function index()
    {
        $integration = Integration::firstOrCreate(
            ['platform' => PlatformEnum::ZAPIER],
            [
                'name' => 'Zapier',
                'status' => IntegrationStatusEnum::DISCONNECTED,
                'is_active' => false,
                'settings' => []
            ]
        );

        return apiResponse([
            'webhook_url' => $integration->settings['webhook_url'] ?? null,
            'api_key' => $integration->settings['api_key'] ?? null,
            'is_active' => $integration->is_active,
        ], 'Zapier settings retrieved');
    }

    /**
     * Update Zapier settings (Webhook URL, Active Status)
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'webhook_url' => 'nullable|url',
            'is_active' => 'boolean'
        ]);

        $integration = Integration::firstOrCreate(
            ['platform' => PlatformEnum::ZAPIER],
            ['name' => 'Zapier']
        );

        $settings = $integration->settings ?? [];
        if ($request->has('webhook_url')) {
            $settings['webhook_url'] = $request->webhook_url;
            $settings['api_key'] = $apiKey = 'zapier_' . Str::random(40);
        }

        $integration->settings = $settings;

        if ($request->has('is_active')) {
            $integration->is_active = $request->is_active;
            // Update status based on logic: if active and configured, CONNECTED.
            // For Webhooks, there isn't really a "connection" check unless we ping.
            // Simplistic: if active, set CONNECTED.
            $integration->status = $request->is_active ? IntegrationStatusEnum::CONNECTED : IntegrationStatusEnum::DISCONNECTED;
        }

        $integration->save();

        return apiResponse([
            'webhook_url' => $integration->settings['webhook_url'] ?? null,
            'api_key' => $integration->settings['api_key'] ?? null,
            'is_active' => $integration->is_active,
        ], 'Zapier settings updated');
    }

    /**
     * Generate a new API Key for Zapier authentication
     */
    public function regenerateApiKey()
    {
        $integration = Integration::firstOrCreate(
            ['platform' => PlatformEnum::ZAPIER],
            ['name' => 'Zapier']
        );

        $apiKey = Str::random(32);
        $settings = $integration->settings ?? [];
        $settings['api_key'] = $apiKey;
        $integration->settings = $settings;
        $integration->save();

        return apiResponse(['api_key' => $apiKey], 'API Key generated');
    }

    /**
     * Zapier Action: Create Contact
     * This endpoint is called by Zapier to create a contact in this tenant.
     */
    public function createContact(Request $request)
    {
        try {
            DB::beginTransaction();

            // Map request to ContactDTO. 
            // We use Request directly assuming Zapier sends matching fields or we allow partials.
            // DTO::fromRequest expects a Request object.

            // If Zapier sends "data" wrapper or flat keys, we need to handle it.
            // Assuming flat keys matching our API (user configures Zapier to match).

            $contactDTO = ContactDTO::fromRequest($request);
            $contact = $this->contactService->store($contactDTO);

            DB::commit();

            // Return just the necessary info
            return apiResponse($contact, 'Contact created successfully', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
