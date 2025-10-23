<?php

namespace TomatoPHP\FilamentTenancy\Resolvers;

use Illuminate\Routing\Route;
use Stancl\Tenancy\Contracts\Tenant;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedByPathException;
use Stancl\Tenancy\Resolvers\PathTenantResolver as BasePathTenantResolver;

class PathTenantResolver extends BasePathTenantResolver
{
    /**
     * Resolve tenant by name field instead of id.
     */
    public function resolveWithoutCache(...$args): Tenant
    {
        /** @var Route $route */
        $route = $args[0];

        if ($name = $route->parameter(static::$tenantParameterName)) {
            $route->forgetParameter(static::$tenantParameterName);

            // Find tenant by name field instead of id
            $tenant = app(config('tenancy.tenant_model'))->where('name', $name)->first();
            
            if ($tenant) {
                return $tenant;
            }
        }

        throw new TenantCouldNotBeIdentifiedByPathException($name ?? 'unknown');
    }
}

