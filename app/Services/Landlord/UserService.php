<?php

namespace App\Services\Landlord;

use App\DTOs\Landlord\ChangePasswordDTO;
use App\DTOs\UserDTO;
use App\Models\Landlord\Filters\UsersFilter;
use App\Models\Landlord\User;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;

class UserService extends BaseService
{
    /**
     * Return the filter class for users.
     */
    protected function getFilterClass(): string
    {
        return UsersFilter::class;
    }

    /**
     * Return the base query for users.
     */
    protected function baseQuery(): Builder
    {
        return User::query();
    }

    public function create(UserDTO $userDTO)
    {
        return $this->getQuery()->create($userDTO->toArray());
    }

    public function changePassword(ChangePasswordDTO $changePasswordDTO)
    {
        $user = $changePasswordDTO->user;
        // Update the password
        $is_updated = $user->update([
            'password' => bcrypt($changePasswordDTO->password),
        ]);
        // Revoke all tokens (optional: for security, logs out all devices)
        if ($changePasswordDTO->logout_other_devices) {
            $user->tokens()->delete();
        }
        if ($is_updated) {
            // Send email
            //            Mail::to($user->email)->queue(new UserCredentialsMail(
            //                user: $user,
            //                password: $changePasswordDTO->password,
            //                loginUrl: null, //todo get it from config and env files
            //            ));
        }
    }
}
