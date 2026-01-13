<?php

namespace App\Http\Controllers\Central\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Auth\PasswordSetupRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordSetupController extends Controller
{
    public function setup(PasswordSetupRequest $request)
    {
        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$tokenData || $request->token !== $tokenData->token) {
            return apiResponse(message: 'Invalid or expired token.', code: 400);
        }

        // Optional: Check expiration (60 minutes)
        // if (Carbon::parse($tokenData->created_at)->addMinutes(60)->isPast()) {
        //     return apiResponse(message: 'Token has expired.', code: 400);
        // }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return apiResponse(message: 'User not found.', code: 404);
        }

        // Update Landlord User
        $user->forceFill([
            'password' => $request->password,
            'remember_token' => Str::random(60),
        ])->save();

        // Sync to Tenant
        $user->load('tenant');
        if ($user->tenant) {
            $user->tenant->run(function () use ($user, $request) {
                $tenantUser = User::where('email', $user->email)->first();
                if ($tenantUser) {
                    $tenantUser->forceFill([
                        'password' => $request->password,
                    ])->save();
                }
            });
        }

        // Delete the token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return apiResponse(message: 'Password has been set successfully.');
    }
}
