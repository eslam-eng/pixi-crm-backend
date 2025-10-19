<?php

namespace App\Http\Controllers\Api\Integrations;

use App\Http\Controllers\Controller;
use App\Models\Tenant\IntegratedForm;
use App\Models\Tenant\Integration;
use App\Http\Resources\Tenant\Integration\IntegratedFormResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IntegratedFormController extends Controller
{
    /**
     * Display a listing of integrated forms with their field mappings.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Get all integrated forms with their field mappings
            $forms = IntegratedForm::with('fieldMappings')->orderBy('id','desc')->paginate(per_page());
            $data = IntegratedFormResource::collection($forms)->response()->getData(true);
            return apiResponse($data, 'Integrated forms retrieved successfully');

        } catch (\Exception $e) {
            return apiResponse([], 'Failed to retrieve integrated forms', 500);
        }
    }

    /**
     * Display the specified integrated form with its field mappings.
     *
     * @param string $formId
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            $form = IntegratedForm::with('fieldMappings')
                ->where('id', $id)
                ->first();

            if (!$form) {
                return apiResponse([], 'Integrated form not found', 404);
            }

            return apiResponse(new IntegratedFormResource($form), 'Integrated form retrieved successfully');

        } catch (\Exception $e) {
            return apiResponse([], 'Failed to retrieve integrated form', 500);
        }
    }

    /**
     * Update the status of an integrated form (activate/deactivate).
     *
     * @param Request $request
     * @param string $formId
     * @return JsonResponse
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        try {
            $request->validate([
                'is_active' => 'required|boolean',
            ]);

            $form = IntegratedForm::where('id', $id)->first();

            if (!$form) {
                return apiResponse([], 'Integrated form not found', 404);
            }

            $form->is_active = $request->is_active;
            $form->save();

            return apiResponse([
                'form_id' => $form->form_id,
                'is_active' => $form->is_active,
                'status_label' => $form->is_active ? 'Active' : 'Paused',
            ], 'Form status updated successfully');

        } catch (\Exception $e) {
            return apiResponse([], 'Failed to update form status', 500);
        }
    }

    /**
     * Remove the specified integrated form.
     *
     * @param string $formId
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $form = IntegratedForm::where('id', $id)->first();

            if (!$form) {
                return apiResponse([], 'Integrated form not found', 404);
            }

            // Delete field mappings first
            $form->fieldMappings()->delete();
            
            // Delete the form
            $form->delete();

            return apiResponse([], 'Integrated form deleted successfully');

        } catch (\Exception $e) {
            return apiResponse([], 'Failed to delete integrated form', 500);
        }
    }
}
