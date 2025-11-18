<?php

use Illuminate\Http\JsonResponse;

if (!function_exists('apiResponse')) {
    function apiResponse($data = null, $message = null, $code = 200): JsonResponse
    {
        $array = [
            'status' => in_array($code, successCode()),
            'message' => $message,
            'data' => $data,
        ];

        return response()->json($array, $code);
    }
}

if (!function_exists('successCode')) {
    function successCode(): array
    {
        return [
            200,
            201,
            202
        ];
    }
}

// if (!function_exists('notifyUser')) {

//     function notifyUser(\App\Models\User $user, $data = [])
//     {
//         $user->notify(new \App\Notifications\GeneralNotification($data));
//     }
// }

if (!function_exists('getLocale')) {

    function getLocale(): string
    {
        return app()->getLocale();
    }
}


if (!function_exists('setLanguage')) {

    function setLanguage(string $locale): void
    {
        app()->setLocale($locale);
    }
}

if (!function_exists('getAuthUser')) {

    function getAuthUser(string $guard = 'sanctum'): \Illuminate\Contracts\Auth\Authenticatable|null|\App\Models\User
    {
        return auth($guard)->user();
    }
}

if (!function_exists('per_page')) {

    function per_page()
    {
        return request()->get('per_page', 10);
    }
}

if (!function_exists('upload')) {

    function upload($file, $dir = '')
    {
        $fileName = time() . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/' . $dir, $fileName);
        return $fileName;
    }
}

if (!function_exists('user_id')) {

    function user_id()
    {
        return  auth('api_tenant')->user()->id;
    }
}
