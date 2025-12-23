<?php
use App\Http\Controllers\Central\Api\ActivationCodeController;
use App\Http\Controllers\Central\Api\AdminAuthController;
use App\Http\Controllers\Central\Api\AdminController;
use App\Http\Controllers\Central\Api\DepartmentController;
use App\Http\Controllers\Central\Api\IndustryController;
use App\Http\Controllers\Central\Api\CountryCodeController;
use App\Http\Controllers\Central\Api\CurrencyController;
use App\Http\Controllers\Central\Api\DiscountCodeController;
use App\Http\Controllers\Central\Api\FeatureController;
use App\Http\Controllers\Central\Api\CoreLandlordController;
use App\Http\Controllers\Central\Api\LocaleController;
use App\Http\Controllers\Central\Api\PayoutSourceController;
use App\Http\Controllers\Central\Api\PlanController;
use App\Http\Controllers\Central\Api\RoleController as RoleCentralController;
use App\Http\Controllers\Central\Api\SourceController;
use App\Http\Controllers\Central\Api\TimeZoneController;
use App\Http\Controllers\Central\Api\Auth\RegisterController;
use App\Http\Controllers\Central\Api\AuthController as centralAuthController;
use App\Http\Controllers\Central\Api\PaymentController;
use App\Http\Controllers\Central\Api\SettingController;
use App\Http\Controllers\Central\Api\SubscriptionController;
use App\Http\Controllers\Central\Api\TenantLookupController;
use App\Http\Controllers\Central\Api\ClientController;

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->name('central.')->group(function () {

        Route::post('tenant/check', [TenantLookupController::class, 'checkTenant']);


        Route::group(['middleware' => 'guest', 'prefix' => 'auth'], function () {
            Route::post('admin/login', AdminAuthController::class);
            Route::post('register', RegisterController::class)->name('landlord.auth.register');
        });

        Route::get('active-plans', [PlanController::class, 'activePlans']);
        Route::get('locales', LocaleController::class);
        Route::get('country-code', CountryCodeController::class);
        Route::get('currencies', CurrencyController::class);
        Route::get('timezones', TimeZoneController::class);

        // for tenant and shared tables for tenant section
        Route::middleware(['auth:sanctum', 'users.only'])->group(function () {
            Route::get('discount-codes/{discount_code}/plans/{plan}', [DiscountCodeController::class, 'validateDiscountCode']);
        });

        Route::group(['middleware' => 'auth:landlord'], function () {
            Route::post('admins/{admin}/status', [AdminController::class, 'toggleStatus']);
            Route::get('admins/profile', [AdminController::class, 'profile']);
            Route::apiResource('admins', AdminController::class);
            Route::put('locale', [AdminController::class, 'updateLocale']);

            Route::get('plans/statics', [PlanController::class, 'statics']);
            Route::apiResource('plans', PlanController::class);
            Route::apiResource('features', FeatureController::class)->only(['index']);

            Route::apiResource('activation-codes', ActivationCodeController::class);

            Route::group(['prefix' => 'activation-codes'], function () {
                Route::get('/get/statics', [ActivationCodeController::class, 'statics']);
                Route::post('/export-codes', [ActivationCodeController::class, 'exportCodes']);
                Route::post('/validation', [ActivationCodeController::class, 'storeValidation']);
                Route::get('generate/code', [ActivationCodeController::class, 'generateCode']);
                Route::post('/multi-update-status', [ActivationCodeController::class, 'multiUpdateStatus']);
                Route::post('/multi-delete', [ActivationCodeController::class, 'multiDelete']);
            });

            Route::apiResource('clients', ClientController::class);

            Route::group(['prefix' => 'source-collections'], function () {
                Route::get('/', [PayoutSourceController::class, 'index']);
                Route::post('/', [PayoutSourceController::class, 'createCollection']);
                Route::get('/{collection_id}', [PayoutSourceController::class, 'details']);
                Route::patch('/{collection_id}/collect', [PayoutSourceController::class, 'markCollected']);
                Route::patch('/{collection_id}/codes/collect', [PayoutSourceController::class, 'collectedSpaceficPayoutItem']);
            });

            Route::get('permissions', [RoleCentralController::class, 'permissionsList']);
            Route::apiResource('roles', RoleCentralController::class);

            // discount codes routes
            Route::apiResource('discount-codes', DiscountCodeController::class);
            Route::group(['prefix' => 'discount-codes'], function () {
                Route::get('generate/code', [DiscountCodeController::class, 'generateCode']);
            });

            //settings
            Route::apiResource('sources', SourceController::class);
            Route::apiResource('departments', DepartmentController::class);
            Route::apiResource('industries', IndustryController::class);

            Route::post('subscriptions/{subscription}/renew', [SubscriptionController::class, 'renew']);
            Route::apiResource('subscriptions', SubscriptionController::class);

        });
        Route::group(['prefix' => 'core'], function () {
            Route::get('plans', [CoreLandlordController::class, 'plans']);
            Route::get('features', [CoreLandlordController::class, 'getFeatures']);
            Route::get('departments', [CoreLandlordController::class, 'departments']);
            Route::get('sources', [CoreLandlordController::class, 'sources']);
            Route::get('industries', [CoreLandlordController::class, 'industries']);
            Route::get('/check-activation-code', [CoreLandlordController::class, 'checkActivationCode']);
            Route::get('/check-discount-code', [CoreLandlordController::class, 'checkDiscountCode']);

        });
    });
}