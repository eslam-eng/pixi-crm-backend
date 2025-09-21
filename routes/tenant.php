<?php

declare(strict_types=1);

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\ClientController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Web\CustomFieldController;
use App\Http\Controllers\Web\IndustryController;
use App\Http\Controllers\Web\LeadController;
use App\Http\Controllers\Web\LocationController;
use App\Http\Controllers\Web\PiplineController;
use App\Http\Controllers\Web\ReasonController;
use App\Http\Controllers\Web\ResourceController;
use App\Http\Controllers\Web\RolePermissionController;
use App\Http\Controllers\Web\ServiceController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

// Route::middleware([
//     'web',
//     InitializeTenancyBySubdomain::class,
//     PreventAccessFromCentralDomains::class,
// ])->group(function () {
//     // Route::get('/', function () {
//     //     return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
//     // });


//     Route::group(['prefix' => 'authentication', 'middleware' => 'guest'], function () {
//         Route::get('login', [AuthController::class, 'loginForm'])->name('login');
//         Route::get('signup', [AuthController::class, 'signupForm'])->name('signup');
//         Route::post('signup', [AuthController::class, 'signup'])->name('signup');
//         Route::post('login', [AuthController::class, 'login'])->name('signin');
//     });

//     //auth routes
//     Route::group(['prefix' => 'dashboard', 'middleware' => 'auth'], function () {
//         Route::get('/', function () {
//             return view('livewire.index');
//         })->name('home');

//         Route::get('profile', [AuthController::class, 'getProfile'])->name('profile.index');
//         Route::put('profile/', [AuthController::class, 'updateProfile'])->name('profile.update');

//         Route::resource('industries', IndustryController::class);
//         Route::resource('services', ServiceController::class);


//         // Route::get('locations/', [LocationController::class, 'createArea'])->name('locations.areas.create');
//         Route::get('locations/cities', [LocationController::class, 'create'])->name('locations.cities.create');
//         Route::get('locations/governorates', [LocationController::class, 'create'])->name('locations.governorates.create');
//         Route::get('locations/countries', [LocationController::class, 'create'])->name('locations.countries.create');
//         Route::get('locations/cities/{location}/edit', [LocationController::class, 'edit'])->name('locations.cities.edit');
//         Route::get('locations/governorates/{location}/edit', [LocationController::class, 'edit'])->name('locations.governorates.edit');
//         Route::get('locations/countries/{location}/edit', [LocationController::class, 'edit'])->name('locations.countries.edit');
//         Route::post('locations/sublocations', [LocationController::class, 'storeSubLocation'])->name('locations.sublocation.store');
//         Route::put('locations/sublocations/{location}', [LocationController::class, 'updateSubLocation'])->name('locations.sublocation.update');
//         Route::resource('locations', LocationController::class)->except('create');

//         Route::resource('reasons', ReasonController::class);
//         Route::resource('resources', ResourceController::class);



//         Route::resource('custom-fields', CustomFieldController::class);
//         Route::resource('clients', ClientController::class);
//         Route::resource('piplines', PiplineController::class);
//         Route::resource('contacts', ContactController::class);
//         Route::resource('leads', LeadController::class);
//         Route::resource('role-permissions', RolePermissionController::class)->parameters([
//             'role-permissions' => 'role'
//         ]);


//         Route::get('role-permissions', [RolePermissionController::class, 'index'])->name('role-permissions.index');
//         Route::get('role-permissions/{role}', [RolePermissionController::class, 'show'])->name('role-permissions.show');
//         Route::put('role-permissions/{role}', [RolePermissionController::class, 'update'])->name('role-permissions.update');


//         Route::get('logout', [AuthController::class, 'logout'])->name('logout');
//     });
// });
