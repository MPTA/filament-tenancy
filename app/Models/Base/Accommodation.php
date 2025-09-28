<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Accommodation extends Model
{
    use HasTranslations, CentralConnection, HasUuids;

    protected $table = 'accommodations';
    public $translatable = ['name', 'content'];

    protected $fillable = [
        'name',
        'content',
        'star_rating',
        'address',
        'latitude',
        'longitude',
        'country_id',
        'province_id',
        'city_id',
        'district_id',
        'external_id',
        'is_active',
    ];

    protected $casts = [
        'name' => 'array',
        'content' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'star_rating' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the country that owns the accommodation.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the province that owns the accommodation.
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * Get the city that owns the accommodation.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the district that owns the accommodation.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get the accommodation prices for this accommodation.
     */
    public function prices(): HasMany
    {
        return $this->hasMany(AccommodationPrice::class);
    }

    /**
     * Get the tenant accommodation prices for this accommodation.
     */
    public function tenantPrices(): HasMany
    {
        return $this->hasMany(\App\Models\Tenants\TenantAccommodationPrice::class);
    }
}
