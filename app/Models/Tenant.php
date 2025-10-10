<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use TomatoPHP\FilamentTenancy\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{

    /**
     * Get the tenant settings.
     */
    public function settings(): HasOne
    {
        return $this->hasOne(TenantSetting::class, 'tenant_id', 'id');
    }
}

