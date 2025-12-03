<?php

namespace App\Http\Controllers\Api;

use App\Enums\BillingCycleEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\Tenant\Template\TemplateDDLResource;
use App\Http\Resources\Tenant\Template\TemplateResource;
use App\Services\CoreService;
use App\Enums\CurrencyEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\ServiceDuration;
use App\Enums\TaskStatusEnum;
use App\Services\Tenant\TemplateService;
use Exception;

class CoreController extends Controller
{
    protected $coreService;
    protected $templateService;

    public function __construct(CoreService $coreService, TemplateService $templateService)
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
                'Service duration retrieved successfully',
                200
            );
        } catch (Exception $e) {
            return apiResponse(
                message: $e->getMessage(),
                code: 500
            );
        }
    }
}