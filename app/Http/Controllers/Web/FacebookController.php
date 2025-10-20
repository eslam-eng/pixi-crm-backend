<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Integration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FacebookController extends Controller
{
    public function showTestPage(Request $request)
    {
        return view('facebook_login');
    }
    public function handleCallback(Request $request)
    {
        // الخطوة 1: الحصول على الـ code من Facebook
        $code = $request->get('code');

        if (!$code) {
            return response()->json(['error' => 'Authorization code not provided'], 400);
        }
        // الخطوة 2: طلب Access Token من Facebook
        $redirectUri = $this->getTenantRedirectUri();
        
        $response = Http::get('https://graph.facebook.com/v20.0/oauth/access_token', [
            'client_id' => env('FACEBOOK_CLIENT_ID'),
            'redirect_uri' => $redirectUri,
            'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
            'code' => $code,
        ]);

        $data = $response->json();

        if (isset($data['error'])) {
            return response()->json(['error' => $data['error']], 400);
        }

        // الخطوة 3: تخزين Access Token
        $accessToken = $data['access_token'];
        $expiresIn   = $data['expires_in'];

        // تخزين التوكن في جدول integrations
        $integration = Integration::where('name', 'Meta (Facebook)')->first();
        if ($integration) {
            $integration->update([
                'access_token' => $accessToken,
                'token_expires_at' => now()->addSeconds($expiresIn)
            ]);
        }

        // الخطوة 4: جلب بيانات المستخدم كمثال
        $userResponse = Http::get('https://graph.facebook.com/me', [
            'access_token' => $accessToken,
            'fields' => 'id,name,email',
        ]);

        $user = $userResponse->json();

        return response()->json([
            'access_token' => $accessToken,
            'expires_in' => $expiresIn,
            'user' => $user,
        ]);
    }

    /**
     * Save Facebook access token from JavaScript SDK
     */
    public function saveToken(Request $request)
    {
        $request->validate([
            'access_token' => 'required|string',
            'user_id' => 'required|string',
            'expires_in' => 'required|integer',
        ]);

        $integration = Integration::where('name', 'Meta (Facebook)')->first();
        
        if (!$integration) {
            return response()->json(['error' => 'Meta (Facebook) integration not found'], 404);
        }

        $integration->update([
            'access_token' => $request->access_token,
            'token_expires_at' => now()->addSeconds($request->expires_in)
        ]);

        return response()->json([
            'message' => 'Token saved successfully',
            'integration' => $integration->fresh()
        ]);
    }

    /**
     * Get Facebook integration status
     */
    public function getStatus()
    {
        $integration = Integration::where('name', 'Meta (Facebook)')->first();
        
        if (!$integration) {
            return response()->json(['error' => 'Meta (Facebook) integration not found'], 404);
        }

        return response()->json([
            'is_connected' => $integration->isConnected(),
            'has_valid_credentials' => $integration->hasValidCredentials(),
            'is_token_expired' => $integration->isTokenExpired(),
            'token_expires_at' => $integration->token_expires_at,
            'token_expires_in' => $integration->token_expires_in,
        ]);
    }

    /**
     * Data Deletion Callback - Required by Facebook for GDPR compliance
     * This endpoint is called by Facebook when a user requests data deletion
     */
    public function dataDeletionCallback(Request $request)
    {
        // Verify the request is from Facebook
        $signedRequest = $request->get('signed_request');
        
        if (!$signedRequest) {
            return response()->json(['error' => 'Missing signed_request'], 400);
        }

        // Parse the signed request
        $data = $this->parseSignedRequest($signedRequest);
        
        if (!$data) {
            return response()->json(['error' => 'Invalid signed_request'], 400);
        }

        $userId = $data['user_id'] ?? null;
        
        if (!$userId) {
            return response()->json(['error' => 'Missing user_id'], 400);
        }

        // Here you would delete the user's data from your database
        // For example:
        // User::where('facebook_id', $userId)->delete();
        // Or mark as deleted for audit purposes:
        // User::where('facebook_id', $userId)->update(['deleted_at' => now()]);

        // Log the deletion request
        \Log::info('Facebook data deletion request', [
            'user_id' => $userId,
            'request_data' => $data,
            'timestamp' => now()
        ]);

        // Return confirmation URL (optional)
        return response()->json([
            'url' => route('facebook.data-deletion-confirmation', ['user_id' => $userId]),
            'confirmation_code' => 'DELETE_' . $userId . '_' . time()
        ]);
    }

    /**
     * Data deletion confirmation page
     */
    public function dataDeletionConfirmation(Request $request, $userId)
    {
        return view('facebook.data-deletion-confirmation', [
            'user_id' => $userId,
            'confirmation_code' => $request->get('confirmation_code')
        ]);
    }

    /**
     * Parse Facebook signed request
     */
    private function parseSignedRequest($signedRequest)
    {
        list($encodedSig, $payload) = explode('.', $signedRequest, 2);
        
        $secret = env('FACEBOOK_CLIENT_SECRET');
        $expectedSig = hash_hmac('sha256', $payload, $secret, true);
        $sig = base64_decode(strtr($encodedSig, '-_', '+/'));
        
        if (hash_equals($expectedSig, $sig)) {
            return json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
        }
        
        return null;
    }

    /**
     * Validate and refresh access token
     */
    public function validateToken(Request $request)
    {
        $integration = Integration::where('name', 'Meta (Facebook)')->first();
        
        if (!$integration || !$integration->access_token) {
            return response()->json(['error' => 'No access token found'], 404);
        }

        // Check if token is expired
        if ($integration->isTokenExpired()) {
            return response()->json(['error' => 'Token expired', 'expired' => true], 401);
        }

        // Validate token with Facebook
        $response = Http::get('https://graph.facebook.com/me', [
            'access_token' => $integration->access_token,
            'fields' => 'id,name,email'
        ]);

        if ($response->successful()) {
            $userData = $response->json();
            return response()->json([
                'valid' => true,
                'user' => $userData,
                'expires_at' => $integration->token_expires_at
            ]);
        }

        return response()->json(['error' => 'Invalid token', 'valid' => false], 401);
    }

    /**
     * Get user permissions
     */
    public function getPermissions(Request $request)
    {
        $integration = Integration::where('name', 'Meta (Facebook)')->first();
        
        if (!$integration || !$integration->access_token) {
            return response()->json(['error' => 'No access token found'], 404);
        }

        $response = Http::get('https://graph.facebook.com/me/permissions', [
            'access_token' => $integration->access_token
        ]);

        if ($response->successful()) {
            $permissions = $response->json();
            return response()->json($permissions);
        }

        return response()->json(['error' => 'Failed to fetch permissions'], 400);
    }

    /**
     * Revoke access token
     */
    public function revokeToken(Request $request)
    {
        $integration = Integration::where('name', 'Meta (Facebook)')->first();
        
        if (!$integration || !$integration->access_token) {
            return response()->json(['error' => 'No access token found'], 404);
        }

        // Revoke token with Facebook
        $response = Http::delete('https://graph.facebook.com/me/permissions', [
            'access_token' => $integration->access_token
        ]);

        // Clear token from database regardless of Facebook response
        $integration->update([
            'access_token' => null,
            'refresh_token' => null,
            'token_expires_at' => null
        ]);

        return response()->json([
            'message' => 'Token revoked successfully',
            'facebook_response' => $response->json()
        ]);
    }

    /**
     * Get tenant-specific redirect URI for Facebook OAuth
     */
    private function getTenantRedirectUri(): string
    {
        $tenant = tenant();
        $baseUrl = config('app.url');
        
        if ($tenant) {
            // For tenant subdomain setup
            $subdomain = $tenant->id;
            $baseUrl = str_replace('://', "://{$subdomain}.", $baseUrl);
        }
        
        return $baseUrl . '/auth/facebook/callback';
    }
}
