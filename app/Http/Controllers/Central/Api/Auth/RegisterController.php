<?php

namespace App\Http\Controllers\Central\Api\Auth;

use App\DTO\Central\UserDTO;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\RegisterRequest;
use App\Http\Resources\Central\AuthUserResource;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Landlord\Actions\Auth\RegisterService;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /**
     * @throws \Throwable
     */
    public function __invoke(RegisterRequest $request, RegisterService $registerService)
    {
        try {
            $userDTO = UserDTO::fromRequest($request);
            $userDTO->create_free_trial = $request->free_trial ?? false;
            // $user = $registerService->handle(registerDTO: $userDTO);
            DB::beginTransaction();
            $user = User::create([
                'first_name' => $userDTO->first_name,
                'last_name' => $userDTO->last_name,
                'email' => $userDTO->email,
                'password' => bcrypt($userDTO->password),
            ]);

            // Generate tenant_id if not provided
            $tenant_id = $userDTO->tenant_id ?? \Illuminate\Support\Str::uuid();

            $tenant_record = Tenant::create([
                'id' => $tenant_id,
                'tenancy_db_name' => "billiqa_" . $userDTO->organization_name,
                'organization_name' => $userDTO->organization_name,
                'user_id' => $user->id,
            ]);

            $tenant_record->domains()->create([
                'domain' => $userDTO->organization_name,
                'tenant_id' => $tenant_record->id,
            ]);

            $data = [
                'token' => $user->generateToken(),
                'user' => AuthUserResource::make($user),
            ];

            DB::commit();

            return ApiResponse::success(data: $data);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);

            return ApiResponse::error(message: 'there is an error please try again later or contact with support for fast response');
        }
    }
}
