<?php

namespace App\Services\Tenant;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class WhatsAppService
{
    private ?string $apiUrl;
    private ?string $apiKey;
    private ?string $apiToken;

    public function __construct()
    {
        // Configure WhatsApp API credentials from environment
        // You can use services like Twilio, WhatsApp Business API, or custom providers
        $this->apiUrl = config('whatsapp.api_url');
        $this->apiKey = config('whatsapp.api_key');
        $this->apiToken = config('whatsapp.api_token');
    }

    /**
     * Send WhatsApp message
     * 
     * @param string $phone Phone number in international format (e.g., +1234567890)
     * @param string $message Message content
     * @return bool
     * @throws Exception
     */
    public function send(string $phone, string $message): bool
    {
        // Normalize phone number
        $phone = $this->normalizePhoneNumber($phone);

        // If no WhatsApp API is configured, log and return false
        if (!$this->apiUrl || !$this->apiKey) {
            Log::warning('WhatsApp API not configured. Message would be sent to: ' . $phone);
            // In development, you might want to return true for testing
            return config('app.debug', false);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                'to' => $phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent successfully', [
                    'phone' => $phone,
                    'response' => $response->json()
                ]);
                return true;
            }

            Log::error('Failed to send WhatsApp message', [
                'phone' => $phone,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return false;
        } catch (Exception $e) {
            Log::error('WhatsApp service exception', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Normalize phone number to international format
     */
    private function normalizePhoneNumber(string $phone): string
    {
        // Remove all non-digit characters except +
        $phone = preg_replace('/[^\d+]/', '', $phone);

        // If phone doesn't start with +, assume it needs country code
        // You can customize this based on your requirements
        if (!str_starts_with($phone, '+')) {
            // Add default country code if needed (e.g., +1 for US)
            $defaultCountryCode = config('whatsapp.default_country_code', '+1');
            $phone = $defaultCountryCode . $phone;
        }

        return $phone;
    }

    /**
     * Send template message (for WhatsApp Business API)
     */
    public function sendTemplate(string $phone, string $templateName, array $parameters = []): bool
    {
        $phone = $this->normalizePhoneNumber($phone);

        if (!$this->apiUrl || !$this->apiKey) {
            Log::warning('WhatsApp API not configured. Template message would be sent to: ' . $phone);
            return config('app.debug', false);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/template', [
                'to' => $phone,
                'template' => $templateName,
                'parameters' => $parameters,
            ]);

            return $response->successful();
        } catch (Exception $e) {
            Log::error('WhatsApp template service exception', [
                'phone' => $phone,
                'template' => $templateName,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}

