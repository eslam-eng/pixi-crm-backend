<?php

namespace App\Http\Controllers\Central\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Http\Resources\SettingResource;
use Exception;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct() {}

    public function show()
    {
        try {
            $setting = Setting::first(); // always get the first (and only) record
            return ApiResponse(new SettingResource($setting), 'Settings retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'sources' => 'required|string',
                'industries' => 'required|string',
                'departments' => 'required|string',
            ]);

            $setting = Setting::first();
            $setting->update([
                'sources' => array_filter(array_map('trim', explode("\n", $request->input('sources')))),
                'industries' => array_filter(array_map('trim', explode("\n", $request->input('industries')))),
                'departments' => array_filter(array_map('trim', explode("\n", $request->input('departments')))),
            ]);
            return ApiResponse(new SettingResource($setting), 'Settings updated successfully');
        } catch (\Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
