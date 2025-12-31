<?php

namespace App\Http\Controllers\Central\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



class TenantLookupController extends Controller
{

    public function checkMail(Request $request)
    {
        return view("emails.central.welcome_mail");
    }
    public function activeMail(Request $request)
    {
        return view("emails.central.active_client_mail");
    }
    public function checkTenant(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->input('email');

        // 1. Check existing mapping in TenantUser
        $tenantUser = \App\Models\Central\TenantUser::where('email', $email)->with('tenant.domains')->first();

        if ($tenantUser && $tenantUser->tenant) {
            $domain = $tenantUser->tenant->domains->first()?->domain;
            if ($domain) {
                return response()->json([
                    'success' => true,
                    'domain' => $domain,
                    'tenant_id' => $tenantUser->tenant_id,
                ]);
            }
        }

        // 2. If not found in mapping, try to find deeper (e.g. if email belongs to a specific tenant owner or is stored in tenant data)
        // This part depends on how you link emails to tenants if not explicitly mapped.
        // Assuming we look for a Tenant where the 'email' key in the JSON data column matches, OR the owner_id matches a user with this email.

        // Search in Tenant's data json column for email
        $tenant = \App\Models\Central\Tenant::whereJsonContains('data->email', $email)->first();

        if (!$tenant) {
            // Search by Owner
            // Assuming there is a User model in central that is used for owners
            $user = \Illuminate\Foundation\Auth\User::where('email', $email)->first();
            if ($user) {
                $tenant = \App\Models\Central\Tenant::where('owner_id', $user->id)->first();
            }
        }

        if ($tenant) {
            // Create the mapping for future fast lookup
            \App\Models\Central\TenantUser::firstOrCreate([
                'email' => $email,
                'tenant_id' => $tenant->id,
            ], [
                'name' => $email, // Fallback name
            ]);

            $domain = $tenant->domains()->first()?->domain;
            if ($domain) {
                return response()->json([
                    'success' => true,
                    'domain' => $domain,
                    'tenant_id' => $tenant->id,
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'No tenant found for this email.',
        ], 404);
    }
}
