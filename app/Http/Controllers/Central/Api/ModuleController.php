<?php

namespace App\Http\Controllers\Central\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupedModuleCollection;
use App\Services\ModuleService;

class ModuleController extends Controller
{
    protected ModuleService $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $modules = $this->moduleService->getGroupedModules();            
            return apiResponse($modules, 'Modules retrieved successfully');
        } catch (\Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
