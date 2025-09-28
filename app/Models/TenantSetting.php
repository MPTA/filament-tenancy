<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class TenantSetting extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'currency_id',
        'language_id',
        'country_id',
        'city_id',
        'mobile_number',
        'address',
        'contact_name',
        'phone_number',
        'company_name',
        'company_local_name',
    ];

    /**
     * Get the tenant that owns the settings.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(\TomatoPHP\FilamentTenancy\Models\Tenant::class);
    }

    /**
     * Get the currency for this setting.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Base\Currency::class);
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
}
