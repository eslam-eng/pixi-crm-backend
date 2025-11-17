<?php

use App\Enums\Landlord\SubscriptionBillingCycleEnum;
use App\Models\Central\Plan;
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

if (! function_exists('calculateSubscriptionAmount')) {
    function calculateSubscriptionAmount(Plan $plan, SubscriptionBillingCycleEnum $duration): float
    {
        return match ($duration->value) {
            SubscriptionBillingCycleEnum::MONTHLY->value => $plan->monthly_price,
            SubscriptionBillingCycleEnum::ANNUAL->value => $plan->annual_price,
            SubscriptionBillingCycleEnum::LIFETIME->value => $plan->lifetime_price,
        };
    }
}

if (! function_exists('calculateSubscriptionEndDate')) {
    function calculateSubscriptionEndDate(SubscriptionBillingCycleEnum $duration): ?\DateTime
    {
        return match ($duration->value) {
            SubscriptionBillingCycleEnum::MONTHLY->value => now()->addMonth(),
            SubscriptionBillingCycleEnum::ANNUAL->value => now()->addYear(),
            SubscriptionBillingCycleEnum::LIFETIME->value => null,
        };
    }
}
