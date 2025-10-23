<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
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

// Check if path-based identification is enabled
$prefix = config('filament-tenancy.identification_method') === 'path' ? '/tenants/{tenant}' : '';

Route::prefix($prefix)->middleware([
    'web',
    \TomatoPHP\FilamentTenancy\FilamentTenancyServiceProvider::getTenancyIdentificationMiddleware(),
])->group(function () {
    if(config('filament-tenancy.features.impersonation')) {
        Route::get('/login/url', [\TomatoPHP\FilamentTenancy\Http\Controllers\LoginUrl::class, 'index']);
    }

    // Your Tenant routes here
    // Filament App Panel will be automatically registered by FilamentTenancyAppPlugin

});
