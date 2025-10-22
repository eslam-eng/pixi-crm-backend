<?php

namespace App\Services\Landlord\OAuth;

use App\DTOs\Landlord\ShopifyCallbackDTO;
use App\Enum\ExternalPlatformEnum;
use App\Exceptions\ShopifyOAuthException;
use App\Models\Landlord\Platform;
use App\Models\Landlord\Tenant;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ShopifyOAuthService
{
    /**
     * @throws ShopifyOAuthException
     */
    public function validateHmac(ShopifyCallbackDTO $dto): void
    {
        $queryString = urldecode(http_build_query($dto->requestData, '', '&', PHP_QUERY_RFC3986));
        $calculatedHmac = hash_hmac('sha256', $queryString, config('services.shopify.client_secret'));

        if (!hash_equals($dto->hmac, $calculatedHmac)) {
            throw new ShopifyOAuthException(__('app.integration.shopify_hmac_exception_message'));
        }
    }

    /**
     * @throws ShopifyOAuthException
     * @throws ConnectionException
     */
    public function handleCallback(ShopifyCallbackDTO $dto): array
    {
        $this->validateHmac($dto);

        $tenant = Tenant::find($dto->state);

        if (!$tenant) {
            throw new ShopifyOAuthException(__('app.integration.shopify_tenant_not_exists_exception_message'));
        }

        $tenant->makeCurrent();

        $platform = Platform::query()->firstWhere('slug', ExternalPlatformEnum::SHOPIFY->value);

        if (!$platform) {
            throw new ShopifyOAuthException(__('app.integration.shopify_platform_not_exists_exception_message'));
        }

        $tokenUrl = str_replace('{shop}', $dto->shop, $platform->token_url);

        $response = Http::post($tokenUrl, [
            'client_id' => config('services.shopify.client_id'),
            'client_secret' => config('services.shopify.client_secret'),
            'code' => $dto->code,
        ]);

        if ($response->failed()) {
            throw new ShopifyOAuthException(__('app.integration.shopify_token_exchange_exception_message'));
        }

        $accessToken = $response->json('access_token');

        DB::connection('tenant')
            ->table('tenant_platforms')->updateOrInsert(
                [
                    'tenant_id' => $tenant->id,
                    'platform_id' => $platform->id,
                    'external_id' => $dto->shop,
                ],
                [
                    'access_token' => $accessToken,
                    'status' => 'active',
                    'updated_at' => now(),
                ]
            );

        return [
            'success' => true,
            'message' => 'integrated successfully',
            'error' => [],
        ];
    }
}
