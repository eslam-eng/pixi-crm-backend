<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CoreService;
use App\Enums\CurrencyEnum;
use Exception;

class CoreController extends Controller
{
    protected $coreService;

    public function __construct(CoreService $coreService)
    {
        $this->coreService = $coreService;
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
}