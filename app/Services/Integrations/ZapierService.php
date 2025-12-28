<?php

namespace App\Services\Integrations;

use App\Models\Tenant\Integration;
use App\Enums\PlatformEnum;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZapierService
{
    /**
     * Send an event to Zapier Webhook
     *
     * @param string $eventName The name of the event (e.g., 'contact.created')
     * @param array $data The data payload to send
     * @return bool
     */
    public function sendEvent(string $eventName, array $data): bool
    {
        try {
            // Find the active Zapier integration
            $integration = Integration::where('platform', PlatformEnum::ZAPIER)
                ->where('is_active', true)
                ->first();

            // Check if webhook URL is configured
            if (!$integration || empty($integration->settings['webhook_url'])) {
                return false;
            }

            $webhookUrl = $integration->settings['webhook_url'];

            // Send the POST request
            $response = Http::post($webhookUrl, [
                'event' => $eventName,
                'data' => $data,
                'timestamp' => now()->toIso8601String(),
                'tenant_id' => tenant('id'), // If using Stancl/Tenancy
            ]);

            if ($response->successful()) {
                // Optionally log success
                return true;
            } else {
                Log::warning("Zapier Webhook Failed: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Zapier Webhook Error: " . $e->getMessage());
            return false;
        }
    }
}
