<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Attraction extends Model
{
    use HasTranslations, CentralConnection, HasUuids;

    protected $table = 'attractions';
    public $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'description',
        'address',
        'latitude',
        'longitude',
        'country_id',
        'province_id',
        'city_id',
        'district_id',
        'rating',
        'external_id',
        'is_active',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'rating' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the country that owns the attraction.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the province that owns the attraction.
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * Get the city that owns the attraction.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the district that owns the attraction.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get the sub attractions for the attraction.
     */
    public function subAttractions(): HasMany
    {
        return $this->hasMany(SubAttraction::class);
    }
}
