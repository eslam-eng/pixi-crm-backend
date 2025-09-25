<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\SettingRequest;
use App\Settings\DealsSettings;
use App\Settings\NotificationSettings;
use App\Settings\TasksSettings;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    public function __construct() {}

    /**
     * Handle switcher action - toggle boolean settings
     */
    public function switcher(Request $request): JsonResponse
    {

        $request->validate([
            'setting' => 'required|string',
            'group' => 'nullable|string'
        ]);

        try {
            $setting = $request->input('setting');
            $group = $request->input('group', 'tasks_settings');

            // Get the appropriate settings class based on group
            $settingsClass = $this->getSettingsClass($group);

            if (!$settingsClass) {
                return apiResponse(message: 'Invalid settings group', code: 400);
            }

            // Get current setting value and toggle it
            $settings = app($settingsClass);
            $currentValue = $settings->$setting;
            $newValue = $currentValue ? false : true; // Toggle

            // Update the setting with the toggled value
            $settings->$setting = $newValue;
            $settings->save();
            $data = [
                'setting' => $setting,
                'previous_value' => $currentValue,
                'new_value' => $newValue
            ];
            return apiResponse($data, trans('app.data changed successfully'));
        } catch (\Exception $e) {
            return apiResponse(message: 'Failed to update setting value: ' . $e->getMessage(), code: 500);
        }
    }

    /**
     * Handle change-value action - update any setting value
     */
    public function changeValue(SettingRequest $request): JsonResponse
    {

        try {
            $setting = $request->input('setting');
            $value = $request->input('value');
            $group = $request->input('group', 'tasks_settings');

            // Get the appropriate settings class based on group
            $settingsClass = $this->getSettingsClass($group);

            if (!$settingsClass) {
                return apiResponse(message: 'Invalid settings group', code: 400);
            }

            // Update the setting
            $settings = app($settingsClass);

            // Handle array values - ensure proper array conversion
            $value = $this->prepareValueForSaving($setting, $value, $settings);

            $settings->$setting = $value;
            $settings->save();
            $data = [
                'setting' => $setting,
                'value' => $value
            ];
            return apiResponse($data, trans('app.data changed successfully'));
        } catch (\Exception $e) {
            return apiResponse(message: 'Failed to update setting value: ' . $e->getMessage(), code: 500);
        }
    }

    /**
     * Get settings by group from request
     */
    public function getSettingsByGroup(Request $request): JsonResponse
    {
        $request->validate([
            'group' => 'required|string'
        ]);

        try {
            $group = $request->input('group', 'tasks_settings');

            // Get the appropriate settings class based on group
            $settingsClass = $this->getSettingsClass($group);

            if (!$settingsClass) {
                return apiResponse(message: 'Invalid settings group', code: 400);
            }

            // Get all settings from the group
            $settings = app($settingsClass);
            $settingsData = $settings->toArray();
            return apiResponse($settingsData, trans('app.data displayed successfully'));
        } catch (\Exception $e) {
            return apiResponse(message: 'Failed to update setting value: ' . $e->getMessage(), code: 500);
        }
    }

    /**
     * Get the appropriate settings class based on group
     */
    private function getSettingsClass(string $group): ?string
    {
        $settingsMap = [
            'tasks_settings' => TasksSettings::class,
            'deals_settings' => DealsSettings::class,
            'notification_settings' => NotificationSettings::class,
            // Add more settings groups as needed
        ];

        return $settingsMap[$group] ?? null;
    }

    /**
     * Prepare value for saving based on the property type
     */
    private function prepareValueForSaving(string $setting, $value, $settings)
    {
        // Handle array values - if the property is typed as array and value is a JSON string, decode it
        $reflection = new \ReflectionClass($settings);
        if ($reflection->hasProperty($setting)) {
            $property = $reflection->getProperty($setting);
            $propertyType = $property->getType();

            if ($propertyType) {
                $typeName = $propertyType->getName();

                // Handle array types
                if ($typeName === 'array' || $typeName === '?array') {
                    if (is_string($value)) {
                        $decodedValue = json_decode($value, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedValue)) {
                            $value = $decodedValue;
                        }
                    }

                    if (is_array($value)) {
                        // Special handling for default_followers_users - convert to integers
                        if ($setting === 'default_followers_users') {
                            if (empty($value)) {
                                return [];
                            }
                            // Convert to integers and filter out invalid values (0, negative)
                            return array_filter(array_map('intval', $value), function ($id) {
                                return $id > 0;
                            });
                        }
                        return $value;
                    }

                    // Handle null values for array properties
                    if (is_null($value) && ($typeName === 'array' || $typeName === '?array')) {
                        return null;
                    }

                    return [];
                }

                // Handle boolean types
                if ($typeName === 'bool' || $typeName === '?bool') {
                    if (is_string($value)) {
                        return in_array(strtolower($value), ['true', '1', 'on', 'yes']);
                    }
                    return (bool) $value;
                }

                // Handle integer types
                if ($typeName === 'int' || $typeName === '?int') {
                    return (int) $value;
                }
            }
        }

        return $value;
    }
}
