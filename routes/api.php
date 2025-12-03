<?php

use App\Http\Controllers\Api\AttendanceController;

use App\Http\Controllers\Api\Automation\AutomationConditionController;
use App\Http\Controllers\Api\Integrations\{
    FacebookController,
    IntegratedFormController
};
use App\Http\Controllers\Api\IntegrationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Central\Api\AdminAuthController;
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
use \App\Http\Controllers\Api\Automation\{
    AutomationActionController,
    AutomationTriggerController,
    AutomationWorkflowController
};
use App\Http\Controllers\Api\CoreController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ItemAttributeController;
use App\Http\Controllers\Api\ItemAttributeValueController;
use App\Http\Controllers\Api\ItemVariantController;
use App\Http\Controllers\Api\TranslatableExampleController;
use App\Http\Controllers\Central\Api\AuthController as centralAuthController;
use App\Http\Controllers\Central\Api\PaymentController;
use App\Http\Controllers\Central\Api\SettingController;
use App\Http\Controllers\Api\SettingController as TenantSettingController;
use App\Http\Controllers\Central\Api\ActivationCodeController;
use App\Http\Controllers\Central\Api\AdminController;
use App\Http\Controllers\Central\Api\CountryCodeController;
use App\Http\Controllers\Central\Api\CurrencyController;
use App\Http\Controllers\Central\Api\DiscountCodeController;
use App\Http\Controllers\Central\Api\FeatureController;
use App\Http\Controllers\Central\Api\LocaleController;
use App\Http\Controllers\Central\Api\PayoutSourceController;
use App\Http\Controllers\Central\Api\PlanController;
use App\Http\Controllers\Central\Api\RoleController as RoleCentralController;
use App\Http\Controllers\Central\Api\SourceController;
use App\Http\Controllers\Central\Api\TimeZoneController;
use App\Http\Controllers\Central\Api\Auth\RegisterController;

// //////////// landlord routes
foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->name('central.')->group(function () {


        Route::group(['middleware' => 'guest', 'prefix' => 'auth'], function () {
            Route::post('admin/login', AdminAuthController::class);
            Route::post('free-trial', RegisterController::class)->name('landlord.auth.free-trial');
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

            Route::group(['prefix' => 'activation-codes'], function () {
                Route::get('/', [ActivationCodeController::class, 'index']);
                Route::get('/statics', [ActivationCodeController::class, 'statics']);
                Route::post('generate', [ActivationCodeController::class, 'store']);
                Route::delete('{activation_code}', [ActivationCodeController::class, 'delete']);
            });


            Route::group(['prefix' => 'source-collections'], function () {
                Route::get('/', [PayoutSourceController::class, 'index']);
                Route::post('/', [PayoutSourceController::class, 'createCollection']);
                Route::get('/{collection_id}', [PayoutSourceController::class, 'details']);
                Route::patch('/{collection_id}/collect', [PayoutSourceController::class, 'markCollected']);
                Route::patch('/{collection_id}/codes/collect', [PayoutSourceController::class, 'collectedSpaceficPayoutItem']);
            });

            Route::get('permissions', [RoleCentralController::class, 'permissionsList']);
            Route::apiResource('roles', RoleCentralController::class);
            Route::apiResource('discount-codes', DiscountCodeController::class);

            Route::apiResource('sources', SourceController::class);
        });
    });
}

// //////////// tenant routes
Route::middleware([
    \Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain::class,
    \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
])->group(function () {
    // Route::group(['prefix' => 'authentication', 'middleware' => 'guest', 'name' => 'authentication.'], function () {
    //     Route::post('/signup', [AuthController::class, 'signup'])->name('tenant.signup');
    // });
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
        Route::post('contacts/duplicate/{id}', [\App\Http\Controllers\Api\ContactMergeController::class, 'duplicateById']);
        Route::post('contacts/duplicate', [\App\Http\Controllers\Api\ContactMergeController::class, 'duplicate']);
        Route::get('contacts/{contact}/details', [\App\Http\Controllers\Api\ContactController::class, 'details']);
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
            Route::get('/user-status', [AttendanceController::class, 'userStatus']);
        });

        Route::post('/users/assign-team', [UserController::class, 'assignToTeam']);
        Route::patch('/users/{user}/end-assignment', [UserController::class, 'endAssignment']);
        Route::get('users/{user}/targets', [UserController::class, 'getTargets']);
        Route::prefix('users/export')->group(function () {
            Route::get('/columns', [\App\Http\Controllers\Api\Users\UserController::class, 'getColumns']);
            Route::post('/', [\App\Http\Controllers\Api\Users\UserController::class, 'export']);
        });
        Route::get('users/{user}/details', [UserController::class, 'details']);
        Route::apiResource('users', UserController::class);
        Route::post('users/{id}/change-active', [UserController::class, 'toggleStatus']);
        Route::get('departments', [DepartmentController::class, 'index']);
        Route::get('roles/permissions/all', [PermissionController::class, 'index']);
        Route::apiResource('roles', RoleController::class);

        //Tasks routes
        Route::apiResource('tasks', TaskController::class);
        Route::get('/tasks/get/statistics', [TaskController::class, 'statistics']);
        Route::post('/tasks/{id}/change-status', [TaskController::class, 'changeStatus']);
        Route::get('/calendar', [TaskController::class, 'calendar']);

        // Deals routes
        Route::apiResource('deals', DealController::class);
        Route::get('deals/get/statistics', [DealController::class, 'statistics']);
        Route::post('deals/{id}/change/approval-status', [DealController::class, 'changeApprovalStatus']);

        // Deal Payments routes
        Route::post('deals/{dealId}/payments', [\App\Http\Controllers\Api\Deals\DealPaymentController::class, 'store']);

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
            Route::get('/templates', [CoreController::class, 'templates']);
            Route::get('/currencies', [CoreController::class, 'getCurrencies']);
            Route::get('/billing-cycles', [CoreController::class, 'getBillingCycle']);
            Route::get('/payment-status', [CoreController::class, 'getPaymentStatus']);
            Route::get('/tasks-status', [CoreController::class, 'getTaskStatus']);
            Route::get('/services-duration', [CoreController::class, 'getServiceDuration']);
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

        // Automation Workflows routes
        Route::apiResource('automation-workflows', AutomationWorkflowController::class);
        Route::get('automation-workflows/get/assigned-strategies', [AutomationWorkflowController::class, 'getAssignedStrategies']);
        Route::patch('automation-workflows/{id}/toggle-active', [AutomationWorkflowController::class, 'toggleActive']);

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

        Route::prefix('dashboard')->group(function () {
            Route::prefix('filter')->group(function () {
                Route::get('/users', [DashboardController::class, 'getfilterUsers']);
                Route::get('/teams', [DashboardController::class, 'getfilterTeams']);
            });
            Route::get('/widgets', [DashboardController::class, 'getWidgets']);
            Route::get('/opportunities-by-stage', [DashboardController::class, 'getOpportunitiesByStage']);
            Route::get('/sale-funnel', [DashboardController::class, 'getSaleFunnel']);
            Route::get('/today-tasks', [DashboardController::class, 'getTodayTasks']);
            Route::get('/recent-activities', [DashboardController::class, 'getUserRecentActivities']);
            Route::get('/get-Target', [DashboardController::class, 'getTarget']);
            Route::get('/top-performing-sales-reps', [DashboardController::class, 'getTopPerformingSalesReps']); // still working on it
        });

        Route::get('/opportunities/kanban-list', [\App\Http\Controllers\Api\OpportunityController::class, 'kanbanList']);
        Route::get('/opportunities/statistics', [\App\Http\Controllers\Api\OpportunityController::class, 'statistics']);
        Route::patch('opportunities/{opportunity}/change-stage', [\App\Http\Controllers\Api\OpportunityController::class, 'changeStage']);
        Route::patch('opportunities/{opportunity}/change-status', [\App\Http\Controllers\Api\OpportunityController::class, 'changeStatus']);
        Route::get('opportunities/{opportunity}/activities-list', [\App\Http\Controllers\Api\OpportunityController::class, 'getActivitiesList']);
        Route::post('opportunities/{opportunity}/log-call', [\App\Http\Controllers\Api\OpportunityController::class, 'logCall']);
        Route::post('opportunities/{opportunity}/add-activity-log', [\App\Http\Controllers\Api\OpportunityController::class, 'AddActivityLog']);
        Route::apiResource('opportunities', \App\Http\Controllers\Api\OpportunityController::class);

        Route::get('teams/{team}/with-target', [\App\Http\Controllers\Api\TeamsController::class, 'showWithTarget']);
        Route::post('teams/team-bulk-assign', [\App\Http\Controllers\Api\TeamsController::class, 'teamBulkAssign']);
        Route::put('teams/{team}/team-bulk-update', [\App\Http\Controllers\Api\TeamsController::class, 'teamBulkUpdate']);
        Route::apiResource('teams', \App\Http\Controllers\Api\TeamsController::class);

        // Template routes
        Route::post('templates/send', [\App\Http\Controllers\Api\TemplatesController::class, 'send']);
        Route::get('templates/get-contact-variables', [\App\Http\Controllers\Api\TemplatesController::class, 'getContactKeys']);
        Route::apiResource('templates', \App\Http\Controllers\Api\TemplatesController::class);

        // pipeline and stage routes
        Route::apiResource('pipelines', \App\Http\Controllers\Api\PipelineController::class);
        Route::patch('pipelines/{pipeline}/update-default', [\App\Http\Controllers\Api\PipelineController::class, 'updateDefault']);
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

        // location routes
        Route::get('/locations/countries', [\App\Http\Controllers\Api\LocationController::class, 'getCountries']);
        Route::get('/locations/countries/{countryId}/cities', [\App\Http\Controllers\Api\LocationController::class, 'getCities']);
        Route::get('/locations/cities/{cityId}/areas', [\App\Http\Controllers\Api\LocationController::class, 'getAreas']);

        Route::apiResource('sources', \App\Http\Controllers\Api\ResourceController::class);

        Route::get('activities', \App\Http\Controllers\Api\ActivityController::class);
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

        // Report routes
        Route::prefix('reports')->group(function () {
            // Sales Performance Reports
            Route::prefix('sales-performance')->group(function () {
                Route::get('/', [\App\Http\Controllers\Api\Report\SalesPerformanceController::class, 'index']);
                Route::get('/deals-performance', [\App\Http\Controllers\Api\Report\SalesPerformanceController::class, 'dealsPerformance']);
                Route::get('/revenue-analysis', [\App\Http\Controllers\Api\Report\SalesPerformanceController::class, 'revenueAnalysis']);
                Route::get('/pipeline-funnel', [\App\Http\Controllers\Api\Report\SalesPerformanceController::class, 'pipelineFunnel']);
                Route::get('/win-loss-analysis', [\App\Http\Controllers\Api\Report\SalesPerformanceController::class, 'winLossAnalysis']);
                Route::get('/sales-rep-performance', [\App\Http\Controllers\Api\Report\SalesPerformanceController::class, 'salesRepPerformance']);
            });

            // Revenue Analysis Reports
            Route::prefix('revenue-analysis')->group(function () {
                Route::get('/', [\App\Http\Controllers\Api\Report\RevenueAnalysisController::class, 'index']);
                Route::get('/revenue-trends', [\App\Http\Controllers\Api\Report\RevenueAnalysisController::class, 'revenueTrends']);
                Route::get('/revenue-by-product', [\App\Http\Controllers\Api\Report\RevenueAnalysisController::class, 'revenueByProduct']);
                Route::get('/revenue-by-customer-segment', [\App\Http\Controllers\Api\Report\RevenueAnalysisController::class, 'revenueByCustomerSegment']);
                Route::get('/revenue-forecast-vs-actual', [\App\Http\Controllers\Api\Report\RevenueAnalysisController::class, 'revenueForecastVsActual']);
            });

            // Deal Analysis Reports
            Route::prefix('deal-analysis')->group(function () {
                Route::get('/', [\App\Http\Controllers\Api\Report\DealAnalysisController::class, 'index']);
                Route::get('/deals-over-time', [\App\Http\Controllers\Api\Report\DealAnalysisController::class, 'dealsOverTime']);
                Route::get('/deals-by-stage', [\App\Http\Controllers\Api\Report\DealAnalysisController::class, 'dealsByStage']);
                Route::get('/deals-by-source', [\App\Http\Controllers\Api\Report\DealAnalysisController::class, 'dealsBySource']);
                Route::get('/deal-value-by-stage', [\App\Http\Controllers\Api\Report\DealAnalysisController::class, 'dealValueByStage']);
                Route::get('/conversion-funnel', [\App\Http\Controllers\Api\Report\DealAnalysisController::class, 'conversionFunnel']);
            });

            // Win/Loss Analysis Reports
            Route::prefix('win-loss-analysis')->group(function () {
                Route::get('/', [\App\Http\Controllers\Api\Report\WinLossAnalysisController::class, 'index']);
                Route::get('/win-loss-trends', [\App\Http\Controllers\Api\Report\WinLossAnalysisController::class, 'winLossTrends']);
                Route::get('/win-rate-trend', [\App\Http\Controllers\Api\Report\WinLossAnalysisController::class, 'winRateTrend']);
                Route::get('/top-win-reasons', [\App\Http\Controllers\Api\Report\WinLossAnalysisController::class, 'topWinReasons']);
                Route::get('/top-loss-reasons', [\App\Http\Controllers\Api\Report\WinLossAnalysisController::class, 'topLossReasons']);
            });

            // Contact Management Reports
            Route::prefix('contact-management')->group(function () {
                Route::get('/', [\App\Http\Controllers\Api\Report\ContactManagementController::class, 'index']);
                Route::get('/contact-analysis', [\App\Http\Controllers\Api\Report\ContactManagementController::class, 'contactAnalysis']);
                Route::get('/contact-engagement-metrics', [\App\Http\Controllers\Api\Report\ContactManagementController::class, 'contactEngagementMetrics']);
                Route::get('/contact-source-analysis', [\App\Http\Controllers\Api\Report\ContactManagementController::class, 'contactSourceAnalysis']);
            });

            // Contact to Opportunity Conversion Reports
            Route::prefix('contact-opportunity-conversion')->group(function () {
                Route::get('/', [\App\Http\Controllers\Api\Report\ContactOpportunityConversionController::class, 'index']);
                Route::get('/conversion-funnel', [\App\Http\Controllers\Api\Report\ContactOpportunityConversionController::class, 'conversionFunnel']);
                Route::get('/conversion-trends', [\App\Http\Controllers\Api\Report\ContactOpportunityConversionController::class, 'conversionTrends']);
                Route::get('/conversion-by-source', [\App\Http\Controllers\Api\Report\ContactOpportunityConversionController::class, 'conversionBySource']);
            });

            // Contact Overview Reports
            Route::prefix('contact-overview')->group(function () {
                Route::get('/', [\App\Http\Controllers\Api\Report\ContactOverviewController::class, 'index']);
                Route::get('/growth-trends', [\App\Http\Controllers\Api\Report\ContactOverviewController::class, 'growthTrends']);
                Route::get('/source-distribution', [\App\Http\Controllers\Api\Report\ContactOverviewController::class, 'sourceDistribution']);
                Route::get('/type-distribution', [\App\Http\Controllers\Api\Report\ContactOverviewController::class, 'typeDistribution']);
                Route::get('/geographic-distribution', [\App\Http\Controllers\Api\Report\ContactOverviewController::class, 'geographicDistribution']);
                Route::get('/industry-distribution', [\App\Http\Controllers\Api\Report\ContactOverviewController::class, 'industryDistribution']);
                Route::get('/company-size-distribution', [\App\Http\Controllers\Api\Report\ContactOverviewController::class, 'companySizeDistribution']);
                Route::get('/quality-score-distribution', [\App\Http\Controllers\Api\Report\ContactOverviewController::class, 'qualityScoreDistribution']);
            });

            // Opportunity Pipeline Reports
            Route::prefix('opportunity-pipeline')->group(function () {
                Route::get('/', [\App\Http\Controllers\Api\Report\OpportunityPipelineController::class, 'index']);
                Route::get('/pipeline-by-stage', [\App\Http\Controllers\Api\Report\OpportunityPipelineController::class, 'pipelineByStage']);
                Route::get('/opportunity-trends', [\App\Http\Controllers\Api\Report\OpportunityPipelineController::class, 'opportunityTrends']);
                Route::get('/opportunities-by-source', [\App\Http\Controllers\Api\Report\OpportunityPipelineController::class, 'opportunitiesBySource']);
                Route::get('/deal-size-distribution', [\App\Http\Controllers\Api\Report\OpportunityPipelineController::class, 'dealSizeDistribution']);
                Route::get('/sales-velocity', [\App\Http\Controllers\Api\Report\OpportunityPipelineController::class, 'salesVelocity']);
                Route::get('/win-rate-by-stage', [\App\Http\Controllers\Api\Report\OpportunityPipelineController::class, 'winRateByStage']);
                Route::get('/top-sales-reps', [\App\Http\Controllers\Api\Report\OpportunityPipelineController::class, 'topSalesReps']);
                Route::get('/conversion-funnel', [\App\Http\Controllers\Api\Report\OpportunityPipelineController::class, 'conversionFunnel']);
            });

            // Opportunity Forecast Reports
            Route::prefix('opportunity-forecast')->group(function () {
                Route::get('/', [\App\Http\Controllers\Api\Report\OpportunityForecastController::class, 'index']);
                Route::get('/forecast-vs-actual', [\App\Http\Controllers\Api\Report\OpportunityForecastController::class, 'forecastVsActual']);
                Route::get('/weighted-pipeline', [\App\Http\Controllers\Api\Report\OpportunityForecastController::class, 'weightedPipeline']);
                Route::get('/quarterly-forecast', [\App\Http\Controllers\Api\Report\OpportunityForecastController::class, 'quarterlyForecast']);
                Route::get('/forecast-accuracy-trend', [\App\Http\Controllers\Api\Report\OpportunityForecastController::class, 'forecastAccuracyTrend']);
                Route::get('/forecast-by-category', [\App\Http\Controllers\Api\Report\OpportunityForecastController::class, 'forecastByCategory']);
                Route::get('/sales-velocity', [\App\Http\Controllers\Api\Report\OpportunityForecastController::class, 'salesVelocity']);
                Route::get('/pipeline-coverage-ratio', [\App\Http\Controllers\Api\Report\OpportunityForecastController::class, 'pipelineCoverageRatio']);
            });

            // Conversion Rate Analysis Reports
            Route::prefix('conversion-rate-analysis')->group(function () {
                Route::get('/', [\App\Http\Controllers\Api\Report\ConversionRateAnalysisController::class, 'index']);
                Route::get('/conversion-funnel', [\App\Http\Controllers\Api\Report\ConversionRateAnalysisController::class, 'conversionFunnel']);
                Route::get('/stage-conversion-rates', [\App\Http\Controllers\Api\Report\ConversionRateAnalysisController::class, 'stageConversionRates']);
                Route::get('/conversion-trend', [\App\Http\Controllers\Api\Report\ConversionRateAnalysisController::class, 'conversionTrend']);
                Route::get('/conversion-by-source', [\App\Http\Controllers\Api\Report\ConversionRateAnalysisController::class, 'conversionBySource']);
                Route::get('/time-to-conversion', [\App\Http\Controllers\Api\Report\ConversionRateAnalysisController::class, 'timeToConversion']);
                Route::get('/team-performance', [\App\Http\Controllers\Api\Report\ConversionRateAnalysisController::class, 'teamPerformance']);
                Route::get('/monthly-conversion-funnel', [\App\Http\Controllers\Api\Report\ConversionRateAnalysisController::class, 'monthlyConversionFunnel']);
                Route::get('/conversion-by-deal-size', [\App\Http\Controllers\Api\Report\ConversionRateAnalysisController::class, 'conversionByDealSize']);
            });
        });

        // Automation Triggers routes
        Route::prefix('automation-triggers')->group(function () {
            Route::get('/', [AutomationTriggerController::class, 'index']);
            Route::get('/{triggerId}/fields', [AutomationTriggerController::class, 'getFields']);
            Route::get('/fields/{fieldId}/options', [AutomationTriggerController::class, 'getFieldOptions']);
        });

        // Automation Conditions routes
        Route::prefix('automation-conditions')->group(function () {
            Route::get('/operations', [AutomationConditionController::class, 'getOperations']);
        });

        // Automation Actions routes
        Route::prefix('automation-actions')->group(function () {
            Route::get('/', [AutomationActionController::class, 'index']);
        });
    });


});

// Facebook callback routes - outside tenant middleware to handle OAuth callbacks
Route::prefix('facebook')->group(function () {
    Route::get('/callback', [FacebookController::class, 'handleCallback']); // OAuth callback
    Route::post('/data-deletion-callback', [FacebookController::class, 'dataDeletionCallback']);

    Route::get('/webhook', [App\Http\Controllers\Api\FacebookLeadWebhookController::class, 'verify']);
    Route::post('/webhook', [App\Http\Controllers\Api\FacebookLeadWebhookController::class, 'handle']);
});
