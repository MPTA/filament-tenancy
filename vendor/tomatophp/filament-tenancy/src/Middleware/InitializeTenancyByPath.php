<?php

namespace TomatoPHP\FilamentTenancy\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath as BaseInitializeTenancyByPath;
use Stancl\Tenancy\Tenancy;
use TomatoPHP\FilamentTenancy\Resolvers\PathTenantResolver;

class InitializeTenancyByPath extends BaseInitializeTenancyByPath
{
    /**
     * Create a new middleware instance.
     */
    public function __construct(Tenancy $tenancy, PathTenantResolver $resolver)
    {
        $this->tenancy = $tenancy;
        $this->resolver = $resolver;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var \Illuminate\Routing\Route $route */
        $route = $request->route();

        // Only initialize tenancy if the route has the tenant parameter
        // This allows domain/subdomain identification to work when path parameter is absent
        $parameterNames = $route->parameterNames();
        
        if (empty($parameterNames) || $parameterNames[0] !== PathTenantResolver::$tenantParameterName) {
            // No tenant parameter in route, skip this middleware
            return $next($request);
        }

        // Route has tenant parameter, proceed with path-based identification
        return parent::handle($request, $next);
    }
}

