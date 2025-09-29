<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class City extends Model
{
    use HasTranslations, CentralConnection, HasUuids;

    protected $table = 'cities';
    public $translatable = ['name'];

    protected $fillable = ['name', 'code', 'province_id'];

    protected $casts = [
        'name' => 'array',
    ];

    /**
     * Get the province that owns the city.
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * Get the districts for the city.
     */
    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    /**
     * Get the attractions for the city.
     */
    public function attractions(): HasMany
    {
        return $this->hasMany(Attraction::class);
    }

    /**
     * Get the accommodations for the city.
     */
    public function accommodations(): HasMany
    {
        return $this->hasMany(Accommodation::class);
    }

    /**
     * Get cached select options for cities.
     */
    public static function getCachedSelectOptions()
    {
        static $cache = null;
        
        if ($cache === null) {
            $cache = static::query()
                ->select('id', 'name')
                ->orderBy('name')
                ->get()
                ->pluck('name', 'id');
        }
        
        return $cache;
    }

    /**
     * Get cached select options for cities filtered by tenant country.
     */
    public static function getCachedSelectOptionsForTenant()
    {
        static $cache = null;
        
        if ($cache === null) {
            // Get tenant's country from settings
            $tenantSetting = \App\Models\TenantSetting::first();
            $tenantCountryId = $tenantSetting?->country_id;
            
            if (!$tenantCountryId) {
                // If no tenant country set, return all cities
                return static::getCachedSelectOptions();
            }
            
            $cache = static::query()
                ->whereHas('province', function ($query) use ($tenantCountryId) {
                    $query->where('country_id', $tenantCountryId);
                })
                ->select('id', 'name')
                ->orderBy('name')
                ->get()
                ->pluck('name', 'id');
        }
        
        return $cache;
    }

    /**
     * Clear the static cache.
     */
    public static function clearStaticCache()
    {
        static $cache = null;
        $cache = null;
    }

    /**
     * Clear the tenant-specific cache.
     */
    public static function clearTenantCache()
    {
        static $cache = null;
        $cache = null;
    }

    /**
     * Boot method to clear cache on model events.
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function () {
            static::clearStaticCache();
        });

        static::updated(function () {
            static::clearStaticCache();
        });

        static::deleted(function () {
            static::clearStaticCache();
        });
    }
}
