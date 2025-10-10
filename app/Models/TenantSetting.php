<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class TenantSetting extends Model
{
    use BelongsToTenant;

    protected $with = ['country.currency'];

    protected $fillable = [
        'tenant_id',
        'language_id',
        'country_id',
        'city_id',
        'mobile_number',
        'address',
        'contact_name',
        'phone_number',
        'company_name',
        'company_local_name',
        'driver_meal_base_budget',
        'driver_accommodation_base_budget',
        'companion_meal_base_budget',
        'companion_accommodation_base_budget',
    ];

    protected $casts = [
        'driver_meal_base_budget' => 'decimal:2',
        'driver_accommodation_base_budget' => 'decimal:2',
        'companion_meal_base_budget' => 'decimal:2',
        'companion_accommodation_base_budget' => 'decimal:2',
    ];

    /**
     * Get the tenant that owns the settings.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Tenant::class, 'tenant_id', 'id');
    }

    /**
     * Get the currency_id attribute from the country.
     * This accessor allows existing code to continue using $tenantSettings->currency_id
     */
    public function getCurrencyIdAttribute()
    {
        return $this->country?->currency_id;
    }

    /**
     * Get the language for this setting.
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Base\Language::class);
    }

    /**
     * Get the country for this setting.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Base\Country::class);
    }

    /**
     * Get the city for this setting.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Base\City::class);
    }

    /**
     * Boot method to clear city cache when country changes.
     */
    protected static function boot()
    {
        parent::boot();

        static::updated(function ($tenantSetting) {
            // Clear city cache when country_id changes
            if ($tenantSetting->isDirty('country_id')) {
                \App\Models\Base\City::clearTenantCache();
            }
        });
    }
}
