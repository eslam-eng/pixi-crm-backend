<?php

namespace App\Services\Central;

use App\DTO\Central\ChangePasswordDTO;
use App\DTO\Central\UserDTO;
use App\Models\Central\Filters\UsersFilter;
use App\Models\Central\User;
use App\Services\Central\BaseService;
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
