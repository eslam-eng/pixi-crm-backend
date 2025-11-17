<?php

namespace App\Services\Central;

use App\DTO\Central\AuthCredentialsDTO;
use App\Models\Central\User;
use App\Services\Central\UserService;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

readonly class AuthService
{
    /**
     * Inject UsersService via constructor.
     */
    public function __construct(protected UserService $landlordUserService) {}

    /**
     * Attempt to login with credentials and return user or unauthorized exception.
     *
     * @throws UnauthorizedHttpException
     */
    public function authenticate(AuthCredentialsDTO $credentials): User
    {
        $filterKey = preg_match('/^\d+$/', $credentials->identifier) ? 'phone' : 'email';
        $filters = [$filterKey => $credentials->identifier];
        $user = $this->landlordUserService->getQuery($filters)->first();
        if (! $user || ! Hash::check($credentials->password, $user->password)) {
            throw new UnauthorizedHttpException('', __('auth.failed'));
        }

        // Optionally, you can generate a token here if using API tokens
        return $user;
    }
}
