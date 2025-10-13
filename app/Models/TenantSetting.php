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
        'logo',
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
     * Get the logo attribute.
     * Ensures logo is always returned as an array for Filament FileUpload.
     */
    public function getLogoAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }

        $decoded = json_decode($value, true);
        
        // If it's already an array, return it
        if (is_array($decoded)) {
            return $decoded;
        }
        
        // If it's a string (old data or direct string save), wrap in array
        if (is_string($decoded)) {
            return [$decoded];
        }
        
        // If json_decode failed, it's a plain string
        return [$value];
    }

    /**
     * Set the logo attribute.
     * Accepts both string and array, stores as json.
     */
    public function setLogoAttribute($value)
    {
        if (is_null($value) || $value === '') {
            $this->attributes['logo'] = null;
            return;
        }

        // If it's already an array, encode it
        if (is_array($value)) {
            $this->attributes['logo'] = json_encode($value);
            return;
        }

        // If it's a string, wrap in array and encode
        $this->attributes['logo'] = json_encode([$value]);
    }

    /**
     * Get tenant-specific directory path for file uploads.
     * 
     * @param string $subdirectory Optional subdirectory (e.g., 'logos', 'documents', 'images')
     * @return string Full path like 'tenants/balopar/logos'
     */
    public static function getTenantDirectory(string $subdirectory = ''): string
    {
        $tenantId = tenant('id');
        $basePath = "tenants/{$tenantId}";
        
        return $subdirectory ? "{$basePath}/{$subdirectory}" : $basePath;
    }

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
     * Boot method to handle file cleanup and cache clearing.
     */
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($tenantSetting) {
            // Delete old logo file when logo changes
            if ($tenantSetting->isDirty('logo')) {
                $oldLogo = $tenantSetting->getRawOriginal('logo'); // Use raw to get JSON string from DB
                if ($oldLogo) {
                    // Decode old logo value
                    $oldLogoDecoded = json_decode($oldLogo, true);
                    $oldFiles = is_array($oldLogoDecoded) ? $oldLogoDecoded : [$oldLogoDecoded];
                    
                    // Delete old files from storage
                    foreach ($oldFiles as $file) {
                        if ($file && \Illuminate\Support\Facades\Storage::disk('public')->exists($file)) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($file);
                        }
                    }
                }
            }
        });

        static::updated(function ($tenantSetting) {
            // Clear city cache when country_id changes
            if ($tenantSetting->isDirty('country_id')) {
                \App\Models\Base\City::clearTenantCache();
            }
        });
    }
}
