<?php

namespace App\Http\Controllers\Api;

use App\Enums\BillingCycleEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\Tenant\Template\TemplateDDLResource;
use App\Services\CoreService;
use App\Enums\CurrencyEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\ServiceDuration;
use App\Enums\TaskStatusEnum;
use App\Http\Resources\ContactDDLResource;
use App\Http\Resources\ItemDDLResource;
use App\Http\Resources\PipelineDDLResource;
use App\Http\Resources\SourceCollection;
use App\Http\Resources\SourceResource;
use App\Http\Resources\StageDDLResource;
use App\Http\Resources\TeamDDLResource;
use App\Http\Resources\Tenant\ItemCategory\ItemCategoryDDLResource;
use App\Http\Resources\Tenant\Opportunity\OpportunityDDLResource;
use App\Http\Resources\Tenant\Tasks\PriorityResource;
use App\Http\Resources\Tenant\Tasks\TaskTypeResource;
use App\Http\Resources\Tenant\Users\DepartmentDDLResource;
use App\Http\Resources\Tenant\Users\PermissionDDLResource;
use App\Http\Resources\Tenant\Users\RoleDDLResource;
use App\Http\Resources\Tenant\Users\UserDDLResource;
use App\Services\Central\SourceService;
use App\Services\ContactService;
use App\Services\LeadService;
use App\Services\PipelineService;
use App\Services\ResourceService;
use App\Services\StageService;
use App\Services\TeamService;
use App\Services\Tenant\ItemCategoryService;
use App\Services\Tenant\ItemService;
use App\Services\Tenant\Tasks\PriorityService;
use App\Services\Tenant\Tasks\TaskTypeService;
use App\Services\Tenant\TemplateService;
use App\Services\Tenant\Users\DepartmentService;
use App\Services\Tenant\Users\PermissionService;
use App\Services\Tenant\Users\RoleService;
use App\Services\Tenant\Users\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CoreController extends Controller
{
    protected $coreService;
    protected $templateService;

    public function __construct(CoreService $coreService, TemplateService $templateService, public Request $request)
    {
        $this->coreService = $coreService;
        $this->templateService = $templateService;
    }

    /**
     * Get sidebar counts for tasks and opportunities
     */
    public function getSidebarCounts()
    {
        try {
            $counts = $this->coreService->getSidebarCounts();

            return apiResponse(
                $counts,
                'Sidebar counts retrieved successfully',
                200
            );
        } catch (Exception $e) {
            return apiResponse(
                message: $e->getMessage(),
                code: 500
            );
        }
    }

    /**
     * Get available currencies
     */
    public function templates()
    {
        try {
            $templates = $this->templateService->queryGet()->get();
            $data =TemplateDDLResource::collection($templates);
            return apiResponse(
                $data,
                'Templates retrieved successfully',
                200
            );
        } catch (Exception $e) {
            return apiResponse(
                message: $e->getMessage(),
                code: 500
            );
        }
    }

    /**
     * Get available currencies
     */
    public function getCurrencies()
    {
        try {
            $currencies = CurrencyEnum::options();

            return apiResponse(
                $currencies,
                'Currencies retrieved successfully',
                200
            );
        } catch (Exception $e) {
            return apiResponse(
                message: $e->getMessage(),
                code: 500
            );
        }
    }

    public function getBillingCycle()
    {
        try {
            $billing_cycles = BillingCycleEnum::options();

            return apiResponse(
                $billing_cycles,
                'Billing cycles retrieved successfully',
                200
            );
        } catch (Exception $e) {
            return apiResponse(
                message: $e->getMessage(),
                code: 500
            );
        }

    }

    public function getPaymentStatus()
    {
        try {
            $payment_status = PaymentStatusEnum::options();

            return apiResponse(
                $payment_status,
                'Payment status retrieved successfully',
                200
            );
        } catch (Exception $e) {
            return apiResponse(
                message: $e->getMessage(),
                code: 500
            );
        }
    }

    public function getTaskStatus()
    {
        try {
            $task_status = TaskStatusEnum::options();

            return apiResponse(
                $task_status,
                'Task status retrieved successfully',
                200
            );
        } catch (Exception $e) {
            return apiResponse(
                message: $e->getMessage(),
                code: 500
            );
        }
    }

    public function getServiceDuration()
    {
        try {
            $service_duration = ServiceDuration::options();

            return apiResponse(
                $service_duration,
                trans('app.data displayed successfully'),
                200
            );
        } catch (Exception $e) {
            return apiResponse(
                message: $e->getMessage(),
                code: 500
            );
        }
    }

    public function getContacts(ContactService $contactService) : JsonResponse 
    {
        $filters = array_filter($this->request->all(), function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });

        $contacts = $contactService->queryGet(filters: $filters,withRelations:['contactPhones'])
            ->select('id', 'first_name','last_name','company_name','email')->get();

        $data = ContactDDLResource::collection($contacts);
        return apiResponse(
            $data,
            trans('app.data displayed successfully'),
            200
        );
    }

    public function getTeams(TeamService $teamService) : JsonResponse 
    {
        $filters = array_filter($this->request->all(), function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });

        $teams = $teamService->queryGet(filters: $filters)->select('id', 'title')->get();
        $data = TeamDDLResource::collection($teams);
        return apiResponse(
            $data,
            trans('app.data displayed successfully'),
            200
        );
    }

    public function getItems(ItemService $itemService) : JsonResponse 
    {
        $filters = array_filter($this->request->all(), function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });

        $teams = $itemService->queryGet(filters: $filters)->select('id', 'name')->get();
        $data = ItemDDLResource::collection($teams);
        return apiResponse(
            $data,
            trans('app.data displayed successfully'),
            200
        );
    }

    public function getPriorities(PriorityService $priorityService) : JsonResponse 
    {
        $filters = array_filter($this->request->all(), function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });

        $priorities = $priorityService->queryGet(filters: $filters,withRelations:['color'])->get();
        $data = PriorityResource::collection($priorities);
        return apiResponse(
            $data,
            trans('app.data displayed successfully'),
            200
        );
    }

    public function getTaskTypes(TaskTypeService $taskTypeService) : JsonResponse 
    {
        $filters = array_filter($this->request->all(), function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });

        $taskTypes = $taskTypeService->queryGet(filters: $filters)->get();
        $data = TaskTypeResource::collection($taskTypes);
        return apiResponse(
            $data,
            trans('app.data displayed successfully'),
            200
        );
    }

    public function getItemCategories(ItemCategoryService $itemCategoryService) : JsonResponse 
    {
        $filters = array_filter($this->request->all(), function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });

        $itemCategories = $itemCategoryService->queryGet(filters: $filters)->get();
        $data = ItemCategoryDDLResource::collection($itemCategories);
        return apiResponse(
            $data,
            trans('app.data displayed successfully'),
            200
        );
    }

    public function getPipelines(PipelineService $pipelineService) : JsonResponse 
    {
        $filters = array_filter($this->request->all(), function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });

        $pipelines = $pipelineService->queryGet(filters: $filters)->get();
        $data = PipelineDDLResource::collection($pipelines);
        return apiResponse(
            $data,
            trans('app.data displayed successfully'),
            200
        );
    }
    
    public function getStagesPipeline(StageService $stageService) : JsonResponse 
    {
        $filters = array_filter($this->request->all(), function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });

        $pipelines = $stageService->queryGet(filters: $filters)->get();
        $data = StageDDLResource::collection($pipelines);
        return apiResponse(
            $data,
            trans('app.data displayed successfully'),
            200
        );
    }
    
    public function getSources(ResourceService $stageService) : JsonResponse 
    {
        $filters = array_filter($this->request->all(), function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });

        $sources = $stageService->queryGet(filters: $filters)->get();
        $data = SourceResource::collection($sources);
        return apiResponse(
            $data,
            trans('app.data displayed successfully'),
            200
        );
    }
    
    public function getRoles(RoleService $roleService) : JsonResponse 
    {
        $filters = array_filter($this->request->all(), function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });

        $roles = $roleService->queryGet(filters: $filters)->get();
        $data = RoleDDLResource::collection($roles);
        return apiResponse(
            $data,
            trans('app.data displayed successfully'),
            200
        );
    }
    
    public function getPermissions(PermissionService $permissionService) : JsonResponse 
    {
        $filters = array_filter($this->request->all(), function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });

        $permissions = $permissionService->queryGet(filters: $filters)->get();
        $data = PermissionDDLResource::collection($permissions);
        return apiResponse(
            $data,
            trans('app.data displayed successfully'),
            200
        );
    }
    
    public function getDepartments(DepartmentService $departmentService) : JsonResponse 
    {
        $filters = array_filter($this->request->all(), function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });

        $departments = $departmentService->queryGet(filters: $filters)->get();
        $data = DepartmentDDLResource::collection($departments);
        return apiResponse(
            $data,
            trans('app.data displayed successfully'),
            200
        );
    }

    public function getOpportunities(LeadService $opportunityService) : JsonResponse 
    {
        $filters = array_filter($this->request->all(), function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });

        $relations= ['items', 'contact:id,first_name,last_name'];
        $opportunitys = $opportunityService->queryGet(filters: $filters, withRelations: $relations)->get();
        $data = OpportunityDDLResource::collection($opportunitys);
        return apiResponse(
            $data,
            trans('app.data displayed successfully'),
            200
        );
    }

    public function getUsers(UserService $userService): JsonResponse
    {
        $filters = array_filter($this->request->all(), function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });

        $users = $userService->queryGet(filters: $filters)->get();
        $data = UserDDLResource::collection($users);
        return apiResponse(
            $data,
            trans('app.data displayed successfully'),
            200
        );
    }
}