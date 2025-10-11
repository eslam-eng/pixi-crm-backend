<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Integration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookController extends Controller
{
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
     * Save Facebook access token from client
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
     * Get Facebook user profile data
     */
    public function getUserProfile(Request $request)
    {
        $integration = Integration::where('name', 'Meta (Facebook)')->first();
        
        if (!$integration || !$integration->access_token) {
            return response()->json(['error' => 'No access token found'], 404);
        }

        // Check if token is expired
        if ($integration->isTokenExpired()) {
            return response()->json(['error' => 'Token expired', 'expired' => true], 401);
        }

        // Get user profile data
        $response = Http::get('https://graph.facebook.com/me', [
            'access_token' => $integration->access_token,
            'fields' => 'id,name,email,picture'
        ]);

        if ($response->successful()) {
            $userData = $response->json();
            return response()->json([
                'success' => true,
                'user' => $userData
            ]);
        }

        return response()->json(['error' => 'Failed to fetch user profile'], 400);
    }

    /**
     * Get Facebook pages (if user has pages)
     */
    public function getUserPages(Request $request)
    {
        $integration = Integration::where('name', 'Meta (Facebook)')->first();
        
        if (!$integration || !$integration->access_token) {
            return response()->json(['error' => 'No access token found'], 404);
        }

        // Check if token is expired
        if ($integration->isTokenExpired()) {
            return response()->json(['error' => 'Token expired', 'expired' => true], 401);
        }

        // Get user pages
        $response = Http::get('https://graph.facebook.com/me/accounts', [
            'access_token' => $integration->access_token,
            'fields' => 'id,name,category,access_token'
        ]);

        if ($response->successful()) {
            $pagesData = $response->json();
            return response()->json([
                'success' => true,
                'pages' => $pagesData['data'] ?? []
            ]);
        }

        return response()->json(['error' => 'Failed to fetch user pages'], 400);
    }

    /**
     * Post to Facebook page
     */
    public function postToPage(Request $request)
    {
        $request->validate([
            'page_id' => 'required|string',
            'message' => 'required|string',
            'page_access_token' => 'required|string'
        ]);

        $response = Http::post("https://graph.facebook.com/{$request->page_id}/feed", [
            'message' => $request->message,
            'access_token' => $request->page_access_token
        ]);

        if ($response->successful()) {
            $postData = $response->json();
            return response()->json([
                'success' => true,
                'post_id' => $postData['id'],
                'message' => 'Post published successfully'
            ]);
        }

        return response()->json(['error' => 'Failed to post to Facebook page'], 400);
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
     * Get detailed customer profile information
     */
    public function getCustomerProfile(Request $request)
    {
        $integration = Integration::where('name', 'Meta (Facebook)')->first();
        
        if (!$integration || !$integration->access_token) {
            return response()->json(['error' => 'No access token found'], 404);
        }

        if ($integration->isTokenExpired()) {
            return response()->json(['error' => 'Token expired', 'expired' => true], 401);
        }

        // Get detailed user profile
        $response = Http::get('https://graph.facebook.com/me', [
            'access_token' => $integration->access_token,
            'fields' => 'id,name,email,picture,first_name,last_name,middle_name,birthday,gender,location,hometown,website,about,bio'
        ]);

        if ($response->successful()) {
            $userData = $response->json();
            return response()->json([
                'success' => true,
                'customer_profile' => $userData
            ]);
        }

        return response()->json(['error' => 'Failed to fetch customer profile'], 400);
    }

    /**
     * Get customer's Facebook pages with detailed information
     */
    public function getCustomerPages(Request $request)
    {
        $integration = Integration::where('name', 'Meta (Facebook)')->first();
        
        if (!$integration || !$integration->access_token) {
            return response()->json(['error' => 'No access token found'], 404);
        }

        if ($integration->isTokenExpired()) {
            return response()->json(['error' => 'Token expired', 'expired' => true], 401);
        }

        // Get pages with detailed information
        $response = Http::get('https://graph.facebook.com/me/accounts', [
            'access_token' => $integration->access_token,
            'fields' => 'id,name,category,access_token,picture,fan_count,followers_count,about,description,website,phone,emails,location,hours,verification_status'
        ]);

        if ($response->successful()) {
            $pagesData = $response->json();
            return response()->json([
                'success' => true,
                'customer_pages' => $pagesData['data'] ?? [],
                'total_pages' => count($pagesData['data'] ?? [])
            ]);
        }

        return response()->json(['error' => 'Failed to fetch customer pages'], 400);
    }

    /**
     * Get posts from customer's pages
     */
    public function getCustomerPosts(Request $request)
    {
        $request->validate([
            'page_id' => 'required|string',
            'limit' => 'integer|min:1|max:100'
        ]);

        $integration = Integration::where('name', 'Meta (Facebook)')->first();
        
        if (!$integration || !$integration->access_token) {
            return response()->json(['error' => 'No access token found'], 404);
        }

        if ($integration->isTokenExpired()) {
            return response()->json(['error' => 'Token expired', 'expired' => true], 401);
        }

        // Get page posts
        $response = Http::get("https://graph.facebook.com/{$request->page_id}/posts", [
            'access_token' => $integration->access_token,
            'fields' => 'id,message,created_time,updated_time,permalink_url,attachments,shares,comments.summary(true),reactions.summary(true)',
            'limit' => $request->get('limit', 25)
        ]);

        if ($response->successful()) {
            $postsData = $response->json();
            return response()->json([
                'success' => true,
                'posts' => $postsData['data'] ?? [],
                'total_posts' => count($postsData['data'] ?? [])
            ]);
        }

        return response()->json(['error' => 'Failed to fetch page posts'], 400);
    }

    /**
     * Get page insights/analytics
     */
    public function getPageInsights(Request $request)
    {
        $request->validate([
            'page_id' => 'required|string',
            'metric' => 'string|in:page_fans,page_impressions,page_reach,page_engaged_users,page_post_engagements'
        ]);

        $integration = Integration::where('name', 'Meta (Facebook)')->first();
        
        if (!$integration || !$integration->access_token) {
            return response()->json(['error' => 'No access token found'], 404);
        }

        if ($integration->isTokenExpired()) {
            return response()->json(['error' => 'Token expired', 'expired' => true], 401);
        }

        $metric = $request->get('metric', 'page_fans,page_impressions,page_reach');
        
        // Get page insights
        $response = Http::get("https://graph.facebook.com/{$request->page_id}/insights", [
            'access_token' => $integration->access_token,
            'metric' => $metric,
            'period' => 'day',
            'since' => now()->subDays(30)->format('Y-m-d'),
            'until' => now()->format('Y-m-d')
        ]);

        if ($response->successful()) {
            $insightsData = $response->json();
            return response()->json([
                'success' => true,
                'insights' => $insightsData['data'] ?? [],
                'page_id' => $request->page_id
            ]);
        }

        return response()->json(['error' => 'Failed to fetch page insights'], 400);
    }

    /**
     * Get customer's Facebook Ad Accounts
     */
    public function getCustomerAdAccounts(Request $request)
    {
        $integration = Integration::where('name', 'Meta (Facebook)')->first();
        
        if (!$integration || !$integration->access_token) {
            return response()->json(['error' => 'No access token found'], 404);
        }

        if ($integration->isTokenExpired()) {
            return response()->json(['error' => 'Token expired', 'expired' => true], 401);
        }

        // Get ad accounts
        $response = Http::get('https://graph.facebook.com/me/adaccounts', [
            'access_token' => $integration->access_token,
            'fields' => 'id,name,account_id,account_status,currency,timezone_name,business_name,amount_spent,balance'
        ]);

        if ($response->successful()) {
            $adAccountsData = $response->json();
            return response()->json([
                'success' => true,
                'ad_accounts' => $adAccountsData['data'] ?? [],
                'total_accounts' => count($adAccountsData['data'] ?? [])
            ]);
        }

        return response()->json(['error' => 'Failed to fetch ad accounts'], 400);
    }

    /**
     * Get forms from customer's ad accounts (Lead Ads)
     */
    public function getCustomerForms(Request $request)
    {
        $request->validate([
            'ad_account_id' => 'required|string'
        ]);

        $integration = Integration::where('name', 'Meta (Facebook)')->first();
        
        if (!$integration || !$integration->access_token) {
            return response()->json(['error' => 'No access token found'], 404);
        }

        if ($integration->isTokenExpired()) {
            return response()->json(['error' => 'Token expired', 'expired' => true], 401);
        }

        // Get forms from ad account
        $response = Http::get("https://graph.facebook.com/{$request->ad_account_id}/leadgen_forms", [
            'access_token' => $integration->access_token,
            'fields' => 'id,name,status,leads_count,created_time,updated_time,page_id,page_name,privacy_policy_url,legal_content_url'
        ]);

        if ($response->successful()) {
            $formsData = $response->json();
            return response()->json([
                'success' => true,
                'forms' => $formsData['data'] ?? [],
                'total_forms' => count($formsData['data'] ?? []),
                'ad_account_id' => $request->ad_account_id
            ]);
        }

        return response()->json(['error' => 'Failed to fetch forms'], 400);
    }

    /**
     * Get campaigns from customer's ad accounts
     */
    public function getCustomerCampaigns(Request $request)
    {
        $request->validate([
            'ad_account_id' => 'required|string',
            'limit' => 'integer|min:1|max:100'
        ]);

        $integration = Integration::where('name', 'Meta (Facebook)')->first();
        
        if (!$integration || !$integration->access_token) {
            return response()->json(['error' => 'No access token found'], 404);
        }

        if ($integration->isTokenExpired()) {
            return response()->json(['error' => 'Token expired', 'expired' => true], 401);
        }

        // Get campaigns
        $response = Http::get("https://graph.facebook.com/{$request->ad_account_id}/campaigns", [
            'access_token' => $integration->access_token,
            'fields' => 'id,name,status,objective,created_time,updated_time,start_time,end_time,budget_remaining,spend',
            'limit' => $request->get('limit', 25)
        ]);

        if ($response->successful()) {
            $campaignsData = $response->json();
            return response()->json([
                'success' => true,
                'campaigns' => $campaignsData['data'] ?? [],
                'total_campaigns' => count($campaignsData['data'] ?? []),
                'ad_account_id' => $request->ad_account_id
            ]);
        }

        return response()->json(['error' => 'Failed to fetch campaigns'], 400);
    }

    /**
     * Get leads from a specific form
     */
    public function getFormLeads(Request $request)
    {
        $request->validate([
            'form_id' => 'required|string',
            'limit' => 'integer|min:1|max=100'
        ]);

        $integration = Integration::where('name', 'Meta (Facebook)')->first();
        
        if (!$integration || !$integration->access_token) {
            return response()->json(['error' => 'No access token found'], 404);
        }

        if ($integration->isTokenExpired()) {
            return response()->json(['error' => 'Token expired', 'expired' => true], 401);
        }

        // Get leads from form
        $response = Http::get("https://graph.facebook.com/{$request->form_id}/leads", [
            'access_token' => $integration->access_token,
            'fields' => 'id,created_time,field_data',
            'limit' => $request->get('limit', 25)
        ]);

        if ($response->successful()) {
            $leadsData = $response->json();
            return response()->json([
                'success' => true,
                'leads' => $leadsData['data'] ?? [],
                'total_leads' => count($leadsData['data'] ?? []),
                'form_id' => $request->form_id
            ]);
        }

        return response()->json(['error' => 'Failed to fetch form leads'], 400);
    }

    /**
     * Get ad account insights/analytics
     */
    public function getAdAccountInsights(Request $request)
    {
        $request->validate([
            'ad_account_id' => 'required|string',
            'metric' => 'string|in:impressions,clicks,spend,ctr,cpc,cpm,reach,frequency'
        ]);

        $integration = Integration::where('name', 'Meta (Facebook)')->first();
        
        if (!$integration || !$integration->access_token) {
            return response()->json(['error' => 'No access token found'], 404);
        }

        if ($integration->isTokenExpired()) {
            return response()->json(['error' => 'Token expired', 'expired' => true], 401);
        }

        $metric = $request->get('metric', 'impressions,clicks,spend,ctr');
        
        // Get ad account insights
        $response = Http::get("https://graph.facebook.com/{$request->ad_account_id}/insights", [
            'access_token' => $integration->access_token,
            'fields' => $metric,
            'level' => 'account',
            'time_range' => json_encode([
                'since' => now()->subDays(30)->format('Y-m-d'),
                'until' => now()->format('Y-m-d')
            ])
        ]);

        if ($response->successful()) {
            $insightsData = $response->json();
            return response()->json([
                'success' => true,
                'insights' => $insightsData['data'] ?? [],
                'ad_account_id' => $request->ad_account_id
            ]);
        }

        return response()->json(['error' => 'Failed to fetch ad account insights'], 400);
    }

    /**
     * Get Facebook user accounts using saved access token
     */
    public function getFacebookUserAccounts(Request $request)
    {
        $integration = Integration::where('name', 'Meta (Facebook)')->first();
        
        if (!$integration || !$integration->access_token) {
            return response()->json(['error' => 'No Facebook access token found'], 404);
        }

        if ($integration->isTokenExpired()) {
            return response()->json(['error' => 'Facebook token expired', 'expired' => true], 401);
        }

        // Get user's ad accounts
        $response = Http::get('https://graph.facebook.com/me/adaccounts', [
            'access_token' => $integration->access_token,
            'fields' => 'id,name,account_id,account_status,currency,timezone_name,business_name,amount_spent,balance'
        ]);

        if ($response->successful()) {
            $adAccountsData = $response->json();
            return response()->json([
                'success' => true,
                'user_ad_accounts' => $adAccountsData['data'] ?? [],
                'total_accounts' => count($adAccountsData['data'] ?? []),
                'integration_id' => $integration->id
            ]);
        }

        return response()->json(['error' => 'Failed to fetch user ad accounts'], 400);
    }

    /**
     * Get forms from selected ad account using saved access token
     */
    public function getFormsFromAdAccount(Request $request)
    {
        $request->validate([
            'ad_account_id' => 'required|string'
        ]);

        $integration = Integration::where('name', 'Meta (Facebook)')->first();
        
        if (!$integration || !$integration->access_token) {
            return response()->json(['error' => 'No Facebook access token found'], 404);
        }

        if ($integration->isTokenExpired()) {
            return response()->json(['error' => 'Facebook token expired', 'expired' => true], 401);
        }

        // Get forms from ad account
        $response = Http::get("https://graph.facebook.com/{$request->ad_account_id}/leadgen_forms", [
            'access_token' => $integration->access_token,
            'fields' => 'id,name,status,leads_count,created_time,updated_time,page_id,page_name,privacy_policy_url,legal_content_url'
        ]);

        if ($response->successful()) {
            $formsData = $response->json();
            return response()->json([
                'success' => true,
                'ad_account_id' => $request->ad_account_id,
                'forms' => $formsData['data'] ?? [],
                'total_forms' => count($formsData['data'] ?? []),
                'integration_id' => $integration->id
            ]);
        }

        return response()->json(['error' => 'Failed to fetch forms from ad account'], 400);
    }

    /**
     * Get leads from selected form using saved access token
     */
    public function getLeadsFromForm(Request $request)
    {
        $request->validate([
            'form_id' => 'required|string',
            'limit' => 'integer|min:1|max:100'
        ]);

        $integration = Integration::where('name', 'Meta (Facebook)')->first();
        
        if (!$integration || !$integration->access_token) {
            return response()->json(['error' => 'No Facebook access token found'], 404);
        }

        if ($integration->isTokenExpired()) {
            return response()->json(['error' => 'Facebook token expired', 'expired' => true], 401);
        }

        // Get leads from form
        $response = Http::get("https://graph.facebook.com/{$request->form_id}/leads", [
            'access_token' => $integration->access_token,
            'fields' => 'id,created_time,field_data',
            'limit' => $request->get('limit', 25)
        ]);

        if ($response->successful()) {
            $leadsData = $response->json();
            return response()->json([
                'success' => true,
                'form_id' => $request->form_id,
                'leads' => $leadsData['data'] ?? [],
                'total_leads' => count($leadsData['data'] ?? []),
                'integration_id' => $integration->id
            ]);
        }

        return response()->json(['error' => 'Failed to fetch leads from form'], 400);
    }

    /**
     * Validate Facebook access token
     */
    public function validateFacebookToken(Request $request)
    {
        $integration = Integration::where('name', 'Meta (Facebook)')->first();
        
        if (!$integration || !$integration->access_token) {
            return response()->json(['error' => 'No Facebook access token found'], 404);
        }

        if ($integration->isTokenExpired()) {
            return response()->json(['error' => 'Facebook token expired', 'expired' => true], 401);
        }

        // Validate token with Facebook
        $response = Http::get('https://graph.facebook.com/me', [
            'access_token' => $integration->access_token,
            'fields' => 'id,name'
        ]);

        if ($response->successful()) {
            $userData = $response->json();
            return response()->json([
                'success' => true,
                'valid' => true,
                'user_id' => $userData['id'] ?? null,
                'user_name' => $userData['name'] ?? null,
                'integration_id' => $integration->id,
                'token_expires_at' => $integration->token_expires_at
            ]);
        }

        // Get detailed error information
        $errorData = $response->json();
        return response()->json([
            'success' => false,
            'valid' => false,
            'error' => 'Invalid Facebook access token',
            'facebook_error' => $errorData,
            'status_code' => $response->status(),
            'integration_id' => $integration->id
        ], 400);
    }

    /**
     * Get Facebook user profile using saved access token
     */
    public function getFacebookUserProfile(Request $request)
    {
        $integration = Integration::where('name', 'Meta (Facebook)')->first();
        
        if (!$integration || !$integration->access_token) {
            return response()->json(['error' => 'No Facebook access token found'], 404);
        }

        if ($integration->isTokenExpired()) {
            return response()->json(['error' => 'Facebook token expired', 'expired' => true], 401);
        }

        // Get user profile with detailed error handling
        $response = Http::get('https://graph.facebook.com/me', [
            'access_token' => $integration->access_token,
            'fields' => 'id,name,email,picture,first_name,last_name,middle_name,birthday,gender,location,hometown,website,about'
        ]);

        if ($response->successful()) {
            $userData = $response->json();
            return response()->json([
                'success' => true,
                'user_profile' => $userData,
                'integration_id' => $integration->id
            ]);
        }

        // Get detailed error information
        $errorData = $response->json();
        return response()->json([
            'success' => false,
            'error' => 'Failed to fetch user profile',
            'facebook_error' => $errorData,
            'status_code' => $response->status(),
            'integration_id' => $integration->id,
            'debug_info' => [
                'token_exists' => !empty($integration->access_token),
                'token_length' => strlen($integration->access_token ?? ''),
                'token_expires_at' => $integration->token_expires_at,
                'is_expired' => $integration->isTokenExpired()
            ]
        ], 400);
    }

    /**
     * Get Facebook user pages using saved access token
     */
    public function getFacebookUserPages(Request $request)
    {
        $integration = Integration::where('name', 'Meta (Facebook)')->first();
        
        if (!$integration || !$integration->access_token) {
            return response()->json(['error' => 'No Facebook access token found'], 404);
        }

        if ($integration->isTokenExpired()) {
            return response()->json(['error' => 'Facebook token expired', 'expired' => true], 401);
        }

        // Get user pages
        $response = Http::get('https://graph.facebook.com/me/accounts', [
            'access_token' => $integration->access_token,
            'fields' => 'id,name,category,access_token,picture,fan_count,followers_count,about,description,website,phone,emails,location,hours,verification_status'
        ]);

        if ($response->successful()) {
            $pagesData = $response->json();
            return response()->json([
                'success' => true,
                'user_pages' => $pagesData['data'] ?? [],
                'total_pages' => count($pagesData['data'] ?? []),
                'integration_id' => $integration->id
            ]);
        }

        return response()->json(['error' => 'Failed to fetch user pages'], 400);
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
        Log::info('Facebook data deletion request', [
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
     * Handle Facebook OAuth callback
     */
    public function handleCallback(Request $request)
    {
        // Get the authorization code from Facebook
        $code = $request->get('code');
        $error = $request->get('error');

        if ($error) {
            return response()->json([
                'error' => 'Facebook authorization failed',
                'error_description' => $request->get('error_description', 'Unknown error')
            ], 400);
        }

        if (!$code) {
            return response()->json(['error' => 'Authorization code not provided'], 400);
        }

        // Exchange code for access token
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

        // Store access token
        $accessToken = $data['access_token'];
        $expiresIn = $data['expires_in'];

        // Save to database
        $integration = Integration::where('name', 'Meta (Facebook)')->first();
        if ($integration) {
            $integration->update([
                'access_token' => $accessToken,
                'token_expires_at' => now()->addSeconds($expiresIn)
            ]);
        }

        // Get user information
        $userResponse = Http::get('https://graph.facebook.com/me', [
            'access_token' => $accessToken,
            'fields' => 'id,name,email',
        ]);

        $user = $userResponse->json();

        return response()->json([
            'success' => true,
            'message' => 'Facebook OAuth successful',
            'access_token' => $accessToken,
            'expires_in' => $expiresIn,
            'user' => $user,
            'integration' => $integration->fresh()
        ]);
    }

    /**
     * Get Facebook OAuth authorization URL using config_id
     */
    public function getAuthUrl(Request $request)
    {
        $redirectUri = $this->getTenantRedirectUri();
        $configId = env('FACEBOOK_CONFIG_ID', '1699506300921951');
        
        // Use Facebook Login for Business with config_id
        $authUrl = 'https://www.facebook.com/v20.0/dialog/oauth?' . http_build_query([
            'client_id' => env('FACEBOOK_CLIENT_ID'),
            'redirect_uri' => $redirectUri,
            'config_id' => $configId, // Use config_id for business login
            'response_type' => 'code',
            'state' => $request->get('state', 'default_state') // Optional state parameter
        ]);

        return response()->json([
            'auth_url' => $authUrl,
            'redirect_uri' => $redirectUri,
            'config_id' => $configId,
            'app_type' => 'business',
            'login_type' => 'facebook_login_for_business',
            'note' => 'Using Facebook Login for Business with config_id'
        ]);
    }

    /**
     * Get Facebook app configuration for client-side integration
     */
    public function getAppConfig()
    {
        $configId = env('FACEBOOK_CONFIG_ID', '1699506300921951');
        
        return response()->json([
            'app_id' => env('FACEBOOK_CLIENT_ID'),
            'version' => 'v20.0',
            'redirect_uri' => $this->getTenantRedirectUri(),
            'config_id' => $configId,
            'app_type' => 'business',
            'login_type' => 'facebook_login_for_business',
            'note' => 'Using Facebook Login for Business with config_id'
        ]);
    }

    /**
     * Get tenant-specific redirect URI for Facebook OAuth
     */
    private function getTenantRedirectUri(): string
    {
        $tenant = tenant();
        $baseUrl = config('app.url');
        
        // Force HTTPS for production, allow HTTP for development
        if (app()->environment('production')) {
            $baseUrl = str_replace('http://', 'https://', $baseUrl);
        }
        
        if ($tenant) {
            // For tenant subdomain setup
            $subdomain = $tenant->id;
            $baseUrl = str_replace('://', "://{$subdomain}.", $baseUrl);
        }
        
        return $baseUrl . '/api/facebook/callback';
    }
}
