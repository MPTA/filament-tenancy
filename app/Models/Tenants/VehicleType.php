<?php

namespace App\Models\Tenants;

use App\Models\Base\VehicleCategory;
use App\Traits\HasTranslatableFallback;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class VehicleType extends Model
{
    use HasUuids, BelongsToTenant, HasTranslations, HasTranslatableFallback;

    protected $fillable = [
        'vehicle_category_id',
        'name',
        'slug',
        'cover',
        'capacity_from',
        'capacity_to',
        'per_day_price',
        'half_day_price',
        'max_hour_per_day',
        'max_hour_half_day',
        'extra_hour_price',
        'is_vip',
        'airport_transfer_price',
        'tenant_id',
    ];

    protected $casts = [
        'name' => 'array',
        'capacity_from' => 'integer',
        'capacity_to' => 'integer',
        'per_day_price' => 'decimal:2',
        'half_day_price' => 'decimal:2',
        'max_hour_per_day' => 'integer',
        'max_hour_half_day' => 'integer',
        'extra_hour_price' => 'decimal:2',
        'is_vip' => 'boolean',
        'airport_transfer_price' => 'decimal:2',
    ];

    protected $translatable = [
        'name',
    ];


    /**
     * Get the vehicle category that owns this vehicle type.
     */
    public function vehicleCategory(): BelongsTo
    {
        return $this->belongsTo(VehicleCategory::class);
    }

    /**
     * Scope a query to filter by vehicle category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('vehicle_category_id', $categoryId);
    }

    /**
     * Scope a query to filter by capacity range.
     */
    public function scopeByCapacity($query, $capacity)
    {
        return $query->where('capacity_from', '<=', $capacity)
                    ->where('capacity_to', '>=', $capacity);
    }

    /**
     * Scope a query to filter by VIP status.
     */
    public function scopeVip($query)
    {
        return $query->where('is_vip', true);
    }

    /**
     * Scope a query to filter by non-VIP status.
     */
    public function scopeNotVip($query)
    {
        return $query->where('is_vip', false);
    }

    /**
     * Scope a query to filter by price range.
     */
    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('per_day_price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope a query to search vehicle types.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name->en', 'like', "%{$search}%")
              ->orWhere('name->fa', 'like', "%{$search}%")
              ->orWhere('slug', 'like', "%{$search}%");
        });
    }

    /**
     * Get the formatted per day price.
     */
    public function getFormattedPerDayPriceAttribute(): string
    {
        if (!$this->per_day_price) {
            return 'Not specified';
        }
        return number_format((float) $this->per_day_price, 2) . ' USD';
    }

    /**
     * Get the formatted half day price.
     */
    public function getFormattedHalfDayPriceAttribute(): string
    {
        if (!$this->half_day_price) {
            return 'Not specified';
        }
        return number_format((float) $this->half_day_price, 2) . ' USD';
    }

    /**
     * Get the formatted extra hour price.
     */
    public function getFormattedExtraHourPriceAttribute(): string
    {
        if (!$this->extra_hour_price) {
            return 'Not specified';
        }
        return number_format((float) $this->extra_hour_price, 2) . ' USD';
    }

    /**
     * Get the formatted airport transfer price.
     */
    public function getFormattedAirportTransferPriceAttribute(): string
    {
        if (!$this->airport_transfer_price) {
            return 'Not specified';
        }
        return number_format((float) $this->airport_transfer_price, 2) . ' USD';
    }

    /**
     * Get the capacity range as a string.
     */
    public function getCapacityRangeAttribute(): string
    {
        if ($this->capacity_from === $this->capacity_to) {
            return (string) $this->capacity_from;
        }
        return "{$this->capacity_from}-{$this->capacity_to}";
    }

    /**
     * Get the quotation offers using this vehicle type.
     */
    public function quotationOffers(): HasMany
    {
        return $this->hasMany(QuotationOffer::class);
    }
}
