<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use TomatoPHP\FilamentTenancy\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    use HasDomains;

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Prevent tenant name from being changed after creation
        static::updating(function ($tenant) {
            if ($tenant->isDirty('name') && $tenant->getOriginal('name') !== null) {
                // Restore the original name
                $tenant->name = $tenant->getOriginal('name');
            }
        });
    }

    /**
     * Get the tenant settings.
     */
    public function settings(): HasOne
    {
        return $this->hasOne(TenantSetting::class, 'tenant_id', 'id');
    }
}

