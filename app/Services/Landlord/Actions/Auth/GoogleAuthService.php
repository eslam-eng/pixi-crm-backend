<?php

namespace App\Services\Landlord\Actions\Auth;

use App\DTOs\UserDTO;
use App\Enum\AvailableSocialProvidersEnum;
use App\Models\Landlord\SocialLogin;
use App\Models\Landlord\User;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

readonly class GoogleAuthService
{
    public function __construct(protected RegisterService $registerService) {}

    /**
     * Attempt to login with credentials and return user or unauthorized exception.
     *
     * @throws UnauthorizedHttpException
     */
    public function handle(string $access_token): User
    {
        // check that provider name in available providers
        $provider_name = AvailableSocialProvidersEnum::GOOGLE->value;
        $oauthUser = Socialite::driver($provider_name)->userFromToken($access_token);

        $socialAccount = SocialLogin::where('provider_name', $provider_name)
            ->where('provider_id', $oauthUser->getId())
            ->first();

        if ($socialAccount) {
            return $socialAccount->user;
        }

        // You can either create or link to an existing user based on email
        $email = $oauthUser->getEmail();
        $name = $oauthUser->getName();

        $userDTO = new UserDTO(name: $name, organization_name: $name, email: $email);
        // check if user exists before with same email
        $user = User::query()->firstWhere('email', value: $email);
        if (! $user) {
            $user = $this->registerService->handle($userDTO);
            $user->update(['email_verified_at' => now()]);
            //            event(new UserRegistered($user));
        }

        $user->socialLogins()->updateOrCreate([
            'provider_name' => $provider_name,
            'provider_id' => $oauthUser->getId(),
        ], [
            'access_token' => $oauthUser->token,
            'refresh_token' => $oauthUser->refreshToken,
            'token_expires_at' => now()->addSeconds($oauthUser->expiresIn),
        ]);

        return $user;
    }
}
