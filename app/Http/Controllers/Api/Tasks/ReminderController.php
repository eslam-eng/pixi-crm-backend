<?php

namespace App\Http\Controllers\Api\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Resources\Tenant\Tasks\ReminderResource;
use App\Models\Tenant\Reminder;
use App\Services\Tenant\Tasks\ReminderService;
use Exception;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function __construct(public ReminderService $reminderService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $filters = array_filter($request->get('filters', []), function ($value) {
                return ($value !== null && $value !== false && $value !== '');
            });
            $priorities = $this->reminderService->getAll($filters);
            $data =  ReminderResource::collection($priorities);
            return apiResponse($data, trans('app.data displayed successfully'));
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }


    /**
     * Set a reminder as default
     */
    public function setDefault(Request $request, Reminder $reminder)
    {
        // Unset all other defaults
        Reminder::where('is_default', true)->update(['is_default' => false]);
        
        // Set this reminder as default
        $reminder->update(['is_default' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Default reminder updated successfully',
            'data' => new ReminderResource($reminder)
        ]);
    }
}