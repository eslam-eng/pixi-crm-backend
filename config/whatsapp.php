<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp API Configuration
    |--------------------------------------------------------------------------
    |
    | Configure your WhatsApp API provider settings here.
    | You can use services like Twilio, WhatsApp Business API, or custom providers.
    |
    */

    'api_url' => env('WHATSAPP_API_URL'),
    'api_key' => env('WHATSAPP_API_KEY'),
    'api_token' => env('WHATSAPP_API_TOKEN'),
    'default_country_code' => env('WHATSAPP_DEFAULT_COUNTRY_CODE', '+1'),

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Provider
    |--------------------------------------------------------------------------
    |
    | Choose your WhatsApp provider: 'twilio', 'whatsapp_business_api', 'custom'
    |
    */

    'provider' => env('WHATSAPP_PROVIDER', 'custom'),
];
