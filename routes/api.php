<?php

use App\Http\Controllers\Api\AttributeValueController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\FormSubmissionController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\Tasks\{
    PriorityController,
    PriorityColorController,
    ReminderController,
    TaskController,
    TaskTypeController
};
use \App\Http\Controllers\Api\Deals\{
    DealController,
    PaymentMethodController
};
use \App\Http\Controllers\Api\Users\{
    DepartmentController,
    RoleController,
    UserController
};
use App\Http\Controllers\Api\CoreController;
use App\Http\Controllers\Api\ItemAttributeController;
use App\Http\Controllers\Api\ItemAttributeValueController;
use App\Http\Controllers\Api\ItemVariantController;
use App\Http\Controllers\Api\TranslatableExampleController;
use App\Http\Controllers\Central\Api\AuthController as  centralAuthController;
use App\Http\Controllers\Central\Api\PaymentController;
use App\Http\Controllers\Central\Api\SettingController;
use App\Http\Controllers\Api\SettingController as TenantSettingController;
use App\Http\Controllers\Central\Api\SubscriptionController;
use App\Http\Controllers\Central\Api\ModuleController;


// //////////// landlord routes
foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->name('central.')->group(function () {
        Route::group(['prefix' => 'authentication', 'middleware' => 'guest', 'name' => 'authentication.'], function () {
            // Route::post('signup', [centralAuthController::class, 'signup'])->name('signup');
            Route::post('login', [centralAuthController::class, 'login'])->name('login');
            Route::post('logout', [centralAuthController::class, 'logout'])->name('logout');
            Route::get('hi', fn() => \Illuminate\Support\Facades\DB::getDatabaseName());
        });

        Route::post('subscriptions/subscribe', [SubscriptionController::class, 'subscribe']);
        Route::post('tiers/buy', [\App\Http\Controllers\Central\Api\TierController::class, 'buy'])->name('tiers.buy')->middleware('auth:sanctum');
        Route::apiResource('tiers', \App\Http\Controllers\Central\Api\TierController::class);
        Route::apiResource('modules', \App\Http\Controllers\Central\Api\ModuleController::class);
        // Route::apiResource('activation-codes', \App\Http\Controllers\Central\Api\ActivationCodeController::class);


        Route::get('tenants', [\App\Http\Controllers\Central\Api\TenantController::class, 'index'])->name('tenants.index')->middleware('auth:sanctum');

        //auth routes
        Route::group(['prefix' => 'dashboard', 'middleware' => 'auth:sanctum'], function () {

            Route::prefix('packages')->group(function () {
                Route::get('/statistics', [\App\Http\Controllers\Central\Api\PackageController::class, 'get_statistics']);
                Route::get('/', [\App\Http\Controllers\Central\Api\PackageController::class, 'index']);
                Route::get('/{tier}', [\App\Http\Controllers\Central\Api\PackageController::class, 'show']);
                Route::post('/', [\App\Http\Controllers\Central\Api\PackageController::class, 'store']);
                Route::put('/{tier}', [\App\Http\Controllers\Central\Api\PackageController::class, 'update']);
                Route::delete('/{tier}', [\App\Http\Controllers\Central\Api\PackageController::class, 'destroy']);
            });
            Route::get('/settings', [SettingController::class, 'show']);
            Route::put('/settings', [SettingController::class, 'update']);

            Route::get('/clients/statistics', [\App\Http\Controllers\Central\Api\ClientController::class, 'get_statistics']);
            Route::apiResource('clients', \App\Http\Controllers\Central\Api\ClientController::class);

            Route::get('/locations/countries', [\App\Http\Controllers\Central\Api\LocationController::class, 'getCountries']);
            Route::get('/locations/countries/{countryId}/cities', [\App\Http\Controllers\Central\Api\LocationController::class, 'getCities']);

            //subscription routes
            // Route::get('/subscriptions/activation-method', [SubscriptionController::class, 'getActivationMethod']);
            // Route::get('/subscriptions/payment-status', [SubscriptionController::class, 'getPaymentStatus']);
            // Route::get('/subscriptions/subscription-status', [SubscriptionController::class, 'getSubscriptionStatus']);
            // Route::apiResource('subscriptions', SubscriptionController::class);


            Route::prefix('subscriptions')->controller(SubscriptionController::class)->group(function () {
                Route::get('/', 'index');
                Route::get('/{subscription}', 'show');
                Route::post('/', 'store');
                Route::put('/{subscription}', 'update');
                Route::delete('/{subscription}', 'destroy');
                Route::delete('/{subscription}/{client}', 'destroy');
            });

            Route::get('activation-codes/statistics', [\App\Http\Controllers\Central\Api\ActivationCodeController::class, 'get_statistics']);
            Route::apiResource('activation-codes', \App\Http\Controllers\Central\Api\ActivationCodeController::class);
            Route::apiResource('discount-codes', \App\Http\Controllers\Central\Api\DiscountCodeController::class);
            Route::apiResource('invoices', \App\Http\Controllers\Central\Api\InvoiceController::class);

            //payment routes
            Route::post('payment/process', [PaymentController::class, 'paymentProcess']);
            Route::get('payment/callback', [PaymentController::class, 'callback']);

            Route::post('logout', [centralAuthController::class, 'logout'])->name('logout.post');

            Route::prefix('helpers')->group(function () {
                // Form CRUD
                Route::get('/modules', [ModuleController::class, 'index']);
            });
        });
    });
}


// //////////// tenant routes
Route::middleware([
    \Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain::class,
    \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/test', fn() => \Illuminate\Support\Facades\DB::getDatabaseName());

    Route::group(['prefix' => 'authentication', 'middleware' => 'guest', 'name' => 'authentication.'], function () {
        Route::post('/signup', [AuthController::class, 'signup'])->name('tenant.signup');
    });
    Route::post('authentication/login', [AuthController::class, 'login'])->middleware('redirect_if_authenticated:api_tenant')->name('tenant.login');

    Route::prefix('contacts/import')->group(function () {
        Route::post('/preview', [\App\Http\Controllers\Api\ContactController::class, 'importPreview']);
        Route::post('/', [\App\Http\Controllers\Api\ContactController::class, 'import']);
    });

    Route::prefix('contacts/export')->group(function () {
        Route::get('/columns', [\App\Http\Controllers\Api\ContactController::class, 'getColumns']);
        Route::post('/', [\App\Http\Controllers\Api\ContactController::class, 'export']);
    });

    Route::get('/contacts/statistics', [\App\Http\Controllers\Api\ContactController::class, 'get_statistics']);
    Route::get('contacts/contact-methods', [\App\Http\Controllers\Api\ContactController::class, 'getContactMethods']);

    Route::get('contacts/merge-list', [\App\Http\Controllers\Api\ContactMergeController::class, 'mergeList']);
    Route::post('contacts/form', [\App\Http\Controllers\Api\ContactMergeController::class, 'form']);
    Route::post('contacts/merge', [\App\Http\Controllers\Api\ContactMergeController::class, 'merge']);
    Route::post('contacts/merge/{id}', [\App\Http\Controllers\Api\ContactMergeController::class, 'mergeById']);
    Route::post('contacts/merge-ignore', [\App\Http\Controllers\Api\ContactMergeController::class, 'ignore']);
    Route::post('contacts/merge-ignore/{id}', [\App\Http\Controllers\Api\ContactMergeController::class, 'ignoreById']);
    Route::apiResource('contacts', \App\Http\Controllers\Api\ContactController::class);
    Route::prefix('item-attributes')->group(function () {
        Route::get('/', [ItemAttributeController::class, 'index']);
        Route::post('/', [ItemAttributeController::class, 'store']);
        Route::get('/{attribute}', [ItemAttributeController::class, 'show']);
        Route::put('/{attribute}', [ItemAttributeController::class, 'update']);
        Route::delete('/{attribute}', [ItemAttributeController::class, 'destroy']);

        // Attribute values routes
        Route::post('/{attribute}/values', [ItemAttributeValueController::class, 'store']);
        Route::put('/{attribute}/values/{value}', [ItemAttributeValueController::class, 'update']);
        Route::delete('/{attribute}/values/{value}', [ItemAttributeValueController::class, 'destroy']);
    });

    Route::prefix('items')->group(function () {
        Route::post('/bulk-with-variants', [\App\Http\Controllers\Api\ItemController::class, 'bulkStoreWithVariants']);
    });
    Route::get('items/all', [\App\Http\Controllers\Api\ItemVariantController::class, 'getAll']);
    Route::delete('items/variants/{id}', [\App\Http\Controllers\Api\ItemVariantController::class, 'destroyVariant']);
    Route::apiResource('items', \App\Http\Controllers\Api\ItemController::class);
    Route::apiResource('item-categories', \App\Http\Controllers\Api\ItemCategoryController::class);
    Route::apiResource('item-statuses', \App\Http\Controllers\Api\ItemStatusController::class);
    Route::prefix('items/{item}/variants')->group(function () {
        Route::get('/', [ItemVariantController::class, 'index']);
        Route::post('/', [ItemVariantController::class, 'store']); // Create single variant
        Route::get('/{variant}', [ItemVariantController::class, 'show']); // Show variant
        Route::put('/{variant}', [ItemVariantController::class, 'update']); // Update variant
        Route::delete('/{variant}', [ItemVariantController::class, 'destroy']); // Delete variant
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('authentication/logout', [AuthController::class, 'logout']);
        Route::get('/user', function () {
            return response()->json(Auth::user());
        });
        // Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::post('users/{id}/change-active', [UserController::class, 'toggleStatus']);
        Route::get('departments', [DepartmentController::class, 'index']);

        Route::apiResource('roles', RoleController::class);

        Route::apiResource('tasks', TaskController::class);
        Route::get('/tasks/get/statistics', [TaskController::class, 'statistics']);
        Route::post('/tasks/{id}/change-status', [TaskController::class, 'changeStatus']);

        Route::apiResource('custom-fields', \App\Http\Controllers\Api\CustomFieldController::class);
        // });

        // Notification routes
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
            Route::get('/statistics', [NotificationController::class, 'statistics']);
            Route::get('/recent', [NotificationController::class, 'recent']);
            Route::get('/{id}', [NotificationController::class, 'show']);
            Route::patch('/{id}/mark-read', [NotificationController::class, 'markAsRead']);
            Route::patch('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
            Route::delete('/{id}', [NotificationController::class, 'destroy']);
            Route::delete('/delete-all', [NotificationController::class, 'deleteAll']);
        });

        // Core routes
        Route::prefix('core')->group(function () {
            Route::get('/sidebar-counts', [CoreController::class, 'getSidebarCounts']);
            Route::get('/currencies', [CoreController::class, 'getCurrencies']);
        });

        Route::prefix('settings')->group(function () {
            Route::get('get', [TenantSettingController::class, 'getSettingsByGroup']);
            Route::post('switcher', [TenantSettingController::class, 'switcher']);
            Route::post('change-value', [TenantSettingController::class, 'changeValue']);
        });

        Route::apiResource('payment-methods', PaymentMethodController::class);
        Route::patch('/payment-methods/{id}/set-default', [PaymentMethodController::class, 'setDefault']);
        Route::patch('/payment-methods/{id}/set-checked', [PaymentMethodController::class, 'setChecked']);
    });



    Route::prefix('forms')->group(function () {
        // Form CRUD
        Route::get('/', [FormController::class, 'index']);
        Route::post('/', [FormController::class, 'store']);
        Route::get('/{form}', [FormController::class, 'show']);
        Route::put('/{form}', [FormController::class, 'update']);
        Route::delete('/{form}', [FormController::class, 'destroy']);
        Route::patch('/{form}/toggle', [FormController::class, 'toggle']);

        // Submissions
        Route::post('/{slug}/submit', [FormSubmissionController::class, 'submit']);
        Route::get('/{form}/submissions', [FormSubmissionController::class, 'submissions']);
    });

    Route::apiResource('deals', DealController::class);
    Route::get('deals/get/statistics', [DealController::class, 'statistics']);


    Route::get('/opportunities/kanban-list', [\App\Http\Controllers\Api\OpportunityController::class, 'kanbanList'])->middleware('auth:sanctum');
    Route::get('/opportunities/statistics', [\App\Http\Controllers\Api\OpportunityController::class, 'statistics']);
    Route::patch('opportunities/{opportunity}/change-stage', [\App\Http\Controllers\Api\OpportunityController::class, 'changeStage']);
    Route::get('opportunities/{opportunity}/activities-list', [\App\Http\Controllers\Api\OpportunityController::class, 'getActivitiesList']);
    Route::apiResource('opportunities', \App\Http\Controllers\Api\OpportunityController::class);


    Route::apiResource('teams', \App\Http\Controllers\Api\TeamsController::class);
    Route::apiResource('clients', \App\Http\Controllers\Api\ClientController::class);

    // pipeline and stage routes
    Route::apiResource('pipelines', \App\Http\Controllers\Api\PipelineController::class);
    Route::get('pipelines/{pipelineId}/stages', [\App\Http\Controllers\Api\StageController::class, 'index']);
    Route::post('pipelines/{pipelineId}/stages', [\App\Http\Controllers\Api\StageController::class, 'store']);
    Route::get('stages/{stageId}', [\App\Http\Controllers\Api\StageController::class, 'show']);
    Route::put('stages/{stageId}', [\App\Http\Controllers\Api\StageController::class, 'update']);
    Route::delete('stages/{stageId}', [\App\Http\Controllers\Api\StageController::class, 'destroy']);

    // loss reason routes
    Route::get('pipelines/{pipelineId}/loss-reasons', [\App\Http\Controllers\Api\LossReasonController::class, 'index']);
    Route::post('pipelines/{pipelineId}/loss-reasons', [\App\Http\Controllers\Api\LossReasonController::class, 'store']);
    Route::get('loss-reasons/{lossReasonId}', [\App\Http\Controllers\Api\LossReasonController::class, 'show']);
    Route::put('loss-reasons/{lossReasonId}', [\App\Http\Controllers\Api\LossReasonController::class, 'update']);
    Route::delete('loss-reasons/{lossReasonId}', [\App\Http\Controllers\Api\LossReasonController::class, 'destroy']);


    Route::get('/locations/countries', [\App\Http\Controllers\Api\LocationController::class, 'getCountries']);
    Route::get('/locations/countries/{countryId}/cities', [\App\Http\Controllers\Api\LocationController::class, 'getCities']);
    Route::get('/locations/cities/{cityId}/areas', [\App\Http\Controllers\Api\LocationController::class, 'getAreas']);
    Route::apiResource('sources', \App\Http\Controllers\Api\ResourceController::class);
    Route::apiResource('reasons', \App\Http\Controllers\Api\ReasonController::class);

    // Translatable example routes
    Route::prefix('translatable')->group(function () {
        Route::get('/industries', [TranslatableExampleController::class, 'index']);
        Route::post('/industries', [TranslatableExampleController::class, 'store']);
        Route::get('/industries/{industry}', [TranslatableExampleController::class, 'show']);
        Route::put('/industries/{industry}', [TranslatableExampleController::class, 'update']);
        Route::patch('/industries/{industry}/locale', [TranslatableExampleController::class, 'changeLocale']);
    });



    Route::prefix('task-types')->group(function () {
        // Form CRUD
        Route::get('/', [TaskTypeController::class, 'index']);
        Route::patch('/{id}/set-default', [TaskTypeController::class, 'setDefault']);
    });

    // Priority routes
    Route::apiResource('priorities', PriorityController::class);
    Route::patch('priorities/{priority}/set-default', [PriorityController::class, 'setDefault']);
    Route::get('priorities-colors', [PriorityColorController::class, 'index']);

    // Priority Color routes
    Route::get('priority-colors', [PriorityColorController::class, 'index']);
    Route::get('priority-colors/{id}', [PriorityColorController::class, 'show']);

    // Reminder routes
    Route::apiResource('reminders', ReminderController::class);
    Route::patch('reminders/{reminder}/set-default', [ReminderController::class, 'setDefault']);

    // FCM Token routes
    Route::prefix('fcm-tokens')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\FcmTokenController::class, 'index']);
        Route::post('/', [App\Http\Controllers\Api\FcmTokenController::class, 'store']);
        Route::delete('/', [App\Http\Controllers\Api\FcmTokenController::class, 'destroy']);
    });
});
