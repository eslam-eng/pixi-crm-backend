<?php

namespace App\Services\Central;

use App\Exceptions\NotFoundException;
use App\Models\ActivationCode;
use App\Models\Tenant;
use App\Models\User;
use App\Services\BaseService;
use Auth;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class AuthService extends BaseService
{
    public function __construct(public User $model) {}

    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @throws NotFoundException
     */
    public function loginWithEmailOrPhone(string $identifier, string $password): User|Model
    {
        $identifierField = is_numeric($identifier) ? 'phone' : 'email';
        $user = $this->model->where($identifierField, $identifier)->with('roles', 'permissions')->first();
        if (!$user || !Hash::check($password, $user->password)) {
            throw new NotFoundException(__('app.login_failed'));
        }
        return $this->model->where($identifierField, $identifier)->with('roles', 'permissions')->first();
    }

    public function signup(string $name, string $tenant, string $email, string $password, string $activationCode, $image = null): User|Model
    {
        $user = $this->getModel()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $activationCodeModel = ActivationCode::where('code', $activationCode)->first();
        if (!$activationCodeModel || $activationCodeModel->used_at) {
            throw new Exception(__('app.invalid_activation_code'));
        }

        $tenant_record = Tenant::create([
            'id' => $tenant,
            'tenancy_db_name' => "billiqa_" . $tenant,
            'name' => $tenant,
            'user_id' => $user->id
        ]);

        // Handle image upload if provided
        if ($image) {
            $tenant_record->addMedia($image)
                ->toMediaCollection('images');
        }

        $tenant_record->tiers()->sync($activationCodeModel->tier->id);
        $tenant_record->save();
        $activationCodeModel->used_at = now();
        $activationCodeModel->save();

        $tenant_record->domains()->create([
            'domain' => $tenant,
        ]);
        return $user;
    }

    /**
     * delete existing user
     * @param int $id
     * @return bool
     * @throws NotFoundException
     */
    public function destroy(User $user): bool
    {
        $user->delete();
        return true;
    }

    public function updateProfile(array $data): bool
    {
        $this->model->where('id', Auth::id())->update($data);
        return true;
    }

    public function changePassword(User $user, array $data): bool
    {
        if (!Hash::check($data['old_password'], $user->password))
            throw new Exception(trans('app.not_match'));
        $user->update([
            'password' => bcrypt($data['new_password']),
        ]);
        return true;
    }
}
