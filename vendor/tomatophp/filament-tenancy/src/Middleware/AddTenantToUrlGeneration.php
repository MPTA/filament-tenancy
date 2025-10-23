<?php

namespace TomatoPHP\FilamentTenancy\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class AddTenantToUrlGeneration
{
    /**
     * Add tenant parameter to URL generation for path-based routing
     */
    public function handle(Request $request, Closure $next)
    {
        // Only for path-based or 'all'
        $method = config('filament-tenancy.identification_method', 'subdomain');
        if (!in_array($method, ['path', 'all'])) {
            return $next($request);
        }
        
        // Extract tenant from route parameter or from tenancy
        $tenantName = $request->route('tenant');
        
        if (!$tenantName && tenancy()->initialized) {
            $tenantName = tenant('name');
        }
        
        if ($tenantName) {
            // Set default route parameter for tenant - must be before response
            URL::defaults(['tenant' => $tenantName]);
        }
        
        return $next($request);
    }
}

