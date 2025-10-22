<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\Integrations\{
    FacebookController,
    IntegratedFormController
};
use App\Http\Controllers\Api\IntegrationController;
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
    PermissionController,
    RoleController,
    UserController
};
use App\Http\Controllers\Api\CoreController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ItemAttributeController;
use App\Http\Controllers\Api\ItemAttributeValueController;
use App\Http\Controllers\Api\ItemVariantController;
use App\Http\Controllers\Central\Api\LocaleController;
use App\Http\Controllers\Api\TranslatableExampleController;
use App\Http\Controllers\Api\SettingController as TenantSettingController;
use App\Http\Controllers\Central\Api\Auth\RegisterController;
use App\Http\Controllers\Central\Api\Auth\AuthController as CentralAuthController;
use App\Http\Controllers\Central\Api\TimeZoneController;
use App\Http\Controllers\Central\Api\CountryCodeController;
use App\Http\Controllers\Central\Api\CurrencyController;

// //////////// landlord routes
foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        Route::get('locales', LocaleController::class);
        Route::get('country-code', CountryCodeController::class);
        Route::get('currencies', CurrencyController::class);
        Route::get('timezones', TimeZoneController::class);

        Route::group(['middleware' => 'guest', 'prefix' => 'auth'], function () {
            // Route::middleware('throttle:login')->group(function () {

            // Route::post('login', CentralAuthController::class);

            // Route::post('admin/login', AdminAuthController::class);

            // Route::post('free-trial', RegisterController::class)->name('landlord.auth.free-trial');

            Route::post('register-tenant', RegisterController::class);

            // Route::post('signup-activation-code', RegisterWithActivationCodeController::class);
            // });

            // Route::group(['prefix' => 'google'], function () {
            //     Route::get('/', [GoogleAuthController::class, 'redirectToProvider']);
            //     Route::get('callback', [GoogleAuthController::class, 'authenticate']);
            // });

            // Route::prefix('facebook')->group(function () {
            //     Route::get('/', [FacebookController::class, 'redirectToProvider']);
            //     Route::get('callback', [FacebookController::class, 'callback']);
            //     Route::get('delete-data', [FacebookController::class, 'deleteData']);
            //     Route::get('deauthorize', [FacebookController::class, 'deAuthorize']);
            // });

            // Route::prefix('shopify')->group(function () {
            //     Route::get('/', [ShopifyController::class, 'redirectToProvider']);
            //     Route::get('callback', [ShopifyController::class, 'callback']);
            // });

        });

        // for tenant and shared tables for tenant section
        Route::middleware(['auth:sanctum', 'users.only'])->group(function () {
            Route::get('profile', [UserController::class, 'profile']);
            // Route::post('change-password', [UserController::class, 'changePassword']);

            // Route::group(['prefix' => 'subscriptions'], function () {
            //     Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
            //     Route::post('/{subscription_id}/renew', [SubscriptionController::class, 'renew']);
            //     Route::post('/upgrade', [SubscriptionController::class, 'upgrade']);
            // });
            // Route::get('discount-codes/{discount_code}/plans/{plan}', [DiscountCodeController::class, 'validateDiscountCode']);
        });
    });
}


// //////////// tenant routes
Route::middleware([
    \Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain::class,
    \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::group(['prefix' => 'authentication', 'middleware' => 'guest', 'name' => 'authentication.'], function () {
        Route::post('/signup', [AuthController::class, 'signup'])->name('tenant.signup');
    });
    Route::post('authentication/login', [AuthController::class, 'login'])->middleware('redirect_if_authenticated:api_tenant')->name('tenant.login');

    Route::group(['middleware' => 'auth:api_tenant'], function () {
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

        Route::post('authentication/logout', [AuthController::class, 'logout']);
        Route::get('authentication/get/language', [UserController::class, 'getLanguage']);
        Route::post('authentication/change/language', [UserController::class, 'changeLanguage']);

        // Attendance routes
        Route::group(['prefix' => 'attendances'], function () {
            Route::post('/punch', [AttendanceController::class, 'punch']);
            Route::get('/days', [AttendanceController::class, 'index']); // filters
            Route::get('/clicks', [AttendanceController::class, 'clicks']); // filters
        });

        Route::post('/users/assign-team', [UserController::class, 'assignToTeam']);
        Route::patch('/users/{user}/end-assignment', [UserController::class, 'endAssignment']);
        Route::apiResource('users', UserController::class);
        Route::post('users/{id}/change-active', [UserController::class, 'toggleStatus']);
        Route::get('departments', [DepartmentController::class, 'index']);
        Route::get('roles/permissions/all', [PermissionController::class, 'index']);
        Route::apiResource('roles', RoleController::class);

        //Tasks routes
        Route::apiResource('tasks', TaskController::class);
        Route::get('/tasks/get/statistics', [TaskController::class, 'statistics']);
        Route::post('/tasks/{id}/change-status', [TaskController::class, 'changeStatus']);

        // Deals routes
        Route::apiResource('deals', DealController::class);
        Route::get('deals/get/statistics', [DealController::class, 'statistics']);
        Route::post('deals/{id}/change/approval-status', [DealController::class, 'changeApprovalStatus']);

        // Deal Payments routes
        Route::post('deals/{dealId}/payments', [\App\Http\Controllers\Api\Deals\DealPaymentController::class, 'store']);

        Route::apiResource('custom-fields', \App\Http\Controllers\Api\CustomFieldController::class);

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

        // Deals Settings routes
        Route::prefix('payment-methods')->group(function () {
            Route::apiResource('/', PaymentMethodController::class);
            Route::patch('{id}/set-default', [PaymentMethodController::class, 'setDefault']);
            Route::patch('{id}/set-checked', [PaymentMethodController::class, 'setChecked']);
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

        // Integration routes
        Route::get('/integrations', [IntegrationController::class, 'index']);
        Route::get('/integrations/statistics', [IntegrationController::class, 'statistics']);
        Route::apiResource('integrations', IntegrationController::class);
        Route::patch('/integrations/{integration}/toggle-status', [IntegrationController::class, 'toggleStatus']);

        // Integrated Forms API routes
        Route::prefix('integrated-forms')->group(function () {
            Route::get('/', [IntegratedFormController::class, 'index']);
            Route::get('/{id}', [IntegratedFormController::class, 'show']);
            Route::patch('/{id}/status', [IntegratedFormController::class, 'updateStatus']);
            Route::delete('/{id}', [IntegratedFormController::class, 'destroy']);
        });

        // Facebook Integration API routes
        Route::prefix('facebook')->group(function () {
            // Protected routes (authentication required)
            Route::get('/status', [FacebookController::class, 'getStatus']);
            Route::post('/save-token', [FacebookController::class, 'saveToken']);
            Route::get('/validate-token', [FacebookController::class, 'validateToken']);
            Route::get('/permissions', [FacebookController::class, 'getPermissions']);
            Route::get('/user-profile', [FacebookController::class, 'getUserProfile']);
            Route::get('/user-pages', [FacebookController::class, 'getUserPages']);
            Route::post('/post-to-page', [FacebookController::class, 'postToPage']);
            Route::delete('/revoke-token', [FacebookController::class, 'revokeToken']);

            // Customer Data Endpoints
            Route::get('/customer/profile', [FacebookController::class, 'getCustomerProfile']);
            Route::get('/customer/pages', [FacebookController::class, 'getCustomerPages']);
            Route::get('/customer/posts', [FacebookController::class, 'getCustomerPosts']);
            Route::get('/customer/insights', [FacebookController::class, 'getPageInsights']);

            // Facebook Ads Manager Endpoints
            Route::get('/ads/accounts', [FacebookController::class, 'getCustomerAdAccounts']);
            Route::get('/ads/forms', [FacebookController::class, 'getCustomerForms']);
            Route::get('/ads/campaigns', [FacebookController::class, 'getCustomerCampaigns']);
            Route::get('/ads/leads', [FacebookController::class, 'getFormLeads']);
            Route::get('/ads/insights', [FacebookController::class, 'getAdAccountInsights']);

            // Simplified Facebook User Data Endpoints (using saved access token)
            Route::get('/user/validate-token', [FacebookController::class, 'validateFacebookToken']);
            Route::get('/user/pages', [FacebookController::class, 'getFacebookUserPages']);
            Route::get('/user/ad-accounts', [FacebookController::class, 'getFacebookUserAccounts']);
            Route::get('/user/forms', [FacebookController::class, 'getFormsFromAdAccount']);
            Route::get('/user/leads', [FacebookController::class, 'getLeadsFromForm']);

            // Step 1: Get Business Accounts (Business Manager)
            Route::get('/business-accounts', [FacebookController::class, 'getBusinessAccounts']);

            // Step 2: Get Ad Accounts from specific Business Account
            Route::get('/business/ad-accounts', [FacebookController::class, 'getAdAccountsFromBusiness']);

            // Step 3: Pages From Ad Account - using me/accounts endpoint
            Route::get('/pages-from-ad-account', [FacebookController::class, 'pagesFromAdAccount']);

            // Step 4: Get Forms from specific Page
            Route::get('/forms-from-page', [FacebookController::class, 'getFormsFromPage']);

            // Step 5: Get Form Fields from specific Form
            Route::get('/form-fields', [FacebookController::class, 'getFormFields']);

            // Field Mapping APIs
            Route::get('/contacts-columns', [FacebookController::class, 'getContactsColumns']);
            // Step 6: Save Form Mapping
            Route::post('/save-form-mapping', [FacebookController::class, 'saveFormFieldMapping']);


            Route::get('/get-form-mapping', [FacebookController::class, 'getFormFieldMapping']);
            Route::post('/test-mapping-validation', [FacebookController::class, 'testMappingValidation']);

            // Form Mapping Management APIs
            Route::get('/all-form-mappings', [FacebookController::class, 'getAllFormMappings']);
            Route::post('/update-contacts-count', [FacebookController::class, 'updateContactsCount']);
            Route::post('/increment-contacts-count', [FacebookController::class, 'incrementContactsCount']);

            // Step 6: Get Leads from specific Form
            Route::post('/leads-from-form', [FacebookController::class, 'getLeadsFromForm']);
            Route::get('/pages-from-ad-account-alternative', [FacebookController::class, 'getPagesFromAdAccountAlternative']);
            Route::get('/check-permissions', [FacebookController::class, 'checkPermissions']);
        });

        // Public routes (no authentication required)
        Route::prefix('facebook')->group(function () {
            Route::get('/app-config', [FacebookController::class, 'getAppConfig']);
            Route::get('/auth-url', [FacebookController::class, 'getAuthUrl']); // Get OAuth URL
            Route::get('/callback', [FacebookController::class, 'handleCallback']); // OAuth callback
            Route::post('/data-deletion-callback', [FacebookController::class, 'dataDeletionCallback']);
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

        Route::prefix('dashboard')->middleware('auth:api_tenant')->group(function () {
            Route::get('/widgets', [DashboardController::class, 'getWidgets']);
            Route::get('/opportunities-by-stage', [DashboardController::class, 'getOpportunitiesByStage']);
            Route::get('/sale-funnel', [DashboardController::class, 'getSaleFunnel']);
            Route::get('/today-tasks', [DashboardController::class, 'getTodayTasks']);
            Route::get('/recent-activities', [DashboardController::class, 'getUserRecentActivities']);
            // Route::get('/top-performing-sales-reps', [DashboardController::class, '']); // still working on it
        });

        Route::get('/opportunities/kanban-list', [\App\Http\Controllers\Api\OpportunityController::class, 'kanbanList'])->middleware('auth:api_tenant');
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

        // FCM Token routes
        Route::prefix('fcm-tokens')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\FcmTokenController::class, 'index']);
            Route::post('/', [App\Http\Controllers\Api\FcmTokenController::class, 'store']);
            Route::delete('/', [App\Http\Controllers\Api\FcmTokenController::class, 'destroy']);
        });

        // Facebook Webhook Routes (outside API prefix for direct access)
        Route::prefix('facebook')->group(function () {
            Route::get('/webhook', [App\Http\Controllers\Api\FacebookLeadWebhookController::class, 'verify']);
            Route::post('/webhook', [App\Http\Controllers\Api\FacebookLeadWebhookController::class, 'handle']);
        });
    });
});
