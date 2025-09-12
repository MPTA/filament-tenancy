<?php

namespace App\Models\Base;

use App\Enums\AttractionTypeEnum;
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
        'type',
        'local_price',
        'foreigner_price',
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
        'type' => AttractionTypeEnum::class,
        'local_price' => 'decimal:2',
        'foreigner_price' => 'decimal:2',
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

    /**
     * Scope a query to filter by local price range.
     */
    public function scopeByLocalPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('local_price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope a query to filter by foreigner price range.
     */
    public function scopeByForeignerPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('foreigner_price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope a query to filter by maximum local price.
     */
    public function scopeMaxLocalPrice($query, $maxPrice)
    {
        return $query->where('local_price', '<=', $maxPrice);
    }

    /**
     * Scope a query to filter by maximum foreigner price.
     */
    public function scopeMaxForeignerPrice($query, $maxPrice)
    {
        return $query->where('foreigner_price', '<=', $maxPrice);
    }

    /**
     * Get the formatted local price.
     */
    public function getFormattedLocalPriceAttribute(): string
    {
        if (!$this->local_price) {
            return 'Free';
        }
        return number_format((float) $this->local_price, 2) . ' USD';
    }

    /**
     * Get the formatted foreigner price.
     */
    public function getFormattedForeignerPriceAttribute(): string
    {
        if (!$this->foreigner_price) {
            return 'Free';
        }
        return number_format((float) $this->foreigner_price, 2) . ' USD';
    }
}
