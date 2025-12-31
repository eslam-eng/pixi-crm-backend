<?php

namespace App\Http\Controllers\Central\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ZapierController extends Controller
{
    /**
     * Return tenant details for Zapier authentication test.
     */
    public function me(Request $request)
    {
        $tenant = tenant();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $tenant->id,
                'name' => $tenant->name ?? 'Tenant',
                'domain' => $tenant->domains->first()?->domain,
                'logged_in_as' => auth()->user()?->email ?? 'System',
            ]
        ]);
    }
}
