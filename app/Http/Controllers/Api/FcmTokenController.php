<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FcmTokenController extends Controller
{
    /**
     * Register or update FCM token for the authenticated user
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|max:255',
            'device_type' => 'nullable|string|in:android,ios,web',
            'device_id' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            
            // Check if token already exists
            $existingToken = FcmToken::where('token', $request->token)->first();
            
            if ($existingToken) {
                // Update existing token
                $existingToken->update([
                    'user_id' => $user->id,
                    'device_type' => $request->device_type,
                    'device_id' => $request->device_id,
                    'is_active' => true,
                    'last_used_at' => now(),
                ]);
                
                $fcmToken = $existingToken;
            } else {
                // Create new token
                $fcmToken = FcmToken::create([
                    'user_id' => $user->id,
                    'token' => $request->token,
                    'device_type' => $request->device_type,
                    'device_id' => $request->device_id,
                    'is_active' => true,
                    'last_used_at' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'FCM token registered successfully',
                'data' => $fcmToken
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to register FCM token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove FCM token
     */
    public function destroy(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            
            $fcmToken = FcmToken::where('user_id', $user->id)
                ->where('token', $request->token)
                ->first();

            if (!$fcmToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'FCM token not found'
                ], 404);
            }

            $fcmToken->delete();

            return response()->json([
                'success' => true,
                'message' => 'FCM token removed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove FCM token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's FCM tokens
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            $tokens = $user->fcm_tokens()->active()->get();

            return response()->json([
                'success' => true,
                'data' => $tokens
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch FCM tokens',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}