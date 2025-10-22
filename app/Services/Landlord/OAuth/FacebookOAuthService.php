<?php

namespace App\Services\Landlord\OAuth;

use App\Enum\ExternalPlatformEnum;
use App\Exceptions\FacebookOAuthException;
use App\Exceptions\ShopifyOAuthException;
use App\Jobs\UpgradeFacebookTokenJob;
use App\Models\Landlord\Platform;
use App\Models\Landlord\Tenant;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class FacebookOAuthService
{
    /**
     * Attempt to login with credentials and return user or unauthorized exception.
     *
     * @throws UnauthorizedHttpException
     * @throws FacebookOAuthException
     * @throws ConnectionException
     */
    public function handle(string $tenant_id, string $fb_code)
    {
        $platform = Platform::query()->firstWhere('slug', ExternalPlatformEnum::FACEBOOK->value);
        $tenant = Tenant::find($tenant_id);
        if (! $platform) {
            throw new FacebookOAuthException(ShopifyOAuthException::PLATFORM_NOT_FOUND);
        }
        if (! $tenant) {
            throw new FacebookOAuthException('tenant not found');
        }

        $response = $this->getAccessToken(platform: $platform, fb_code: $fb_code);

        $access_token = Arr::get($response, 'access_token');

        if (! $access_token) {
            throw new FacebookOAuthException('cannot get access token please try again later !');
        }

        // store access toke to go through facebook apis
        DB::table('tenant_platforms')->updateOrInsert(
            [
                'tenant_id' => $tenant->id,
                'platform_id' => $platform->id,
            ],
            [
                'access_token' => $access_token,
                'expires_at' => now()->addSeconds($response['expires_in']),
                'status' => 'active',
                'updated_at' => now(),
            ]
        );
        // Step 2: Upgrade in background (queue job)
        dispatch(new UpgradeFacebookTokenJob(tenant_id: $tenant->id, short_lived_access_token: $access_token, platform: $platform));

        return [
            'success' => true,
            'message' => 'integrated successfully',
            'error' => [],
        ];
    }

    /**
     * @return array|mixed
     *
     * @throws ConnectionException
     * @throws FacebookOAuthException
     */
    private function getAccessToken(Platform $platform, string $fb_code): mixed
    {
        $response = Http::get($platform->token_url, [
            'client_id' => config('services.facebook.client_id'),
            'client_secret' => config('services.facebook.client_secret'),
            'redirect_uri' => config('services.facebook.redirect'),
            'code' => $fb_code,
        ]);

        if ($response->failed()) {
            throw new FacebookOAuthException('Failed to exchange code for access token.');
        }

        return $response->json();
    }
}
