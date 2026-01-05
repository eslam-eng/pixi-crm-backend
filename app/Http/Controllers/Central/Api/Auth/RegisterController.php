<?php

namespace App\Http\Controllers\Central\Api\Auth;

use App\DTO\Central\UserDTO;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\RegisterRequest;
use App\Http\Requests\Central\VerifyRegisterRequest;
use App\Mail\Central\VerificationCodeMail;
use App\Services\Central\RegisterService;
use \Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /**
     * @throws \Throwable
     */
    public function __invoke(RegisterRequest $request, RegisterService $registerService)
    {
        try {

            $check = DB::table('password_reset_tokens')->where('email', $request->email)->first();
            if (!$check || $check->token != $request->code) {
                return ApiResponse::error(message: 'Invalid code');
            }

            $userDTO = UserDTO::fromRequest($request);
            $userDTO->create_free_trial = $request->free_trial ?? false;
            $registerService->handle(registerDTO: $userDTO);
            //After add Tenant remove verify token from table ...
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return ApiResponse::success();
        } catch (\Exception $e) {
            return ApiResponse::error(message: 'there is an error please try again later or contact with support for fast response' . $e->getMessage());
        }
    }


    public function verifyRegisterData(VerifyRegisterRequest $request)
    {
        $data = $request->validated();
        if (env('APP_ENV') == 'local') {
            $random_code = 9999;
        } else {
            $random_code = rand(0000, 9999);
        }

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $data['email']],
            ['token' => $random_code, 'created_at' => now()]
        );

        $data['activation_code'] = $random_code;
        if (env('APP_ENV') == 'production') {
            Mail::to($data['email'])
                ->send(new VerificationCodeMail($random_code));
        }
        return ApiResponse::success(data: $data);
    }
}
