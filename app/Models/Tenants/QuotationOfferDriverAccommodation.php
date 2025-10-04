<?php

namespace App\Models\Tenants;

use App\Models\Base\Accommodation;
use App\Models\Base\City;
use App\Models\Base\RoomCategory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOfferDriverAccommodation extends Model
{
    use HasUuids, BelongsToTenant;

    protected $table = 'quotation_offer_driver_accommodations';

    protected $fillable = [
        'quotation_offer_id',
        'accommodation_id',
        'room_category_id',
        'city_id',
        'nights',
        'night_price',
        'is_base_budget',
        'tenant_id',
    ];

    protected $casts = [
        'nights' => 'integer',
        'night_price' => 'decimal:2',
        'is_base_budget' => 'boolean',
    ];

    /**
     * Get the quotation offer that owns this accommodation.
     */
    public function quotationOffer(): BelongsTo
    {
        return $this->belongsTo(QuotationOffer::class);
    }

    /**
     * Get the accommodation for this entry.
     */
    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }

    /**
     * Get the room category for this entry.
     */
    public function roomCategory(): BelongsTo
    {
        return $this->belongsTo(RoomCategory::class);
    }

    /**
     * Get the city for this entry.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the total cost for this accommodation entry.
     */
    public function getTotalCostAttribute(): float
    {
        return $this->nights * $this->night_price;
    }

    /**
     * Get formatted total cost.
     */
    public function getFormattedTotalCostAttribute(): string
    {
        return number_format($this->total_cost, 2);
    }

    /**
     * Get formatted night price.
     */
    public function getFormattedNightPriceAttribute(): string
    {
        return number_format((float) $this->night_price, 2);
    }

    /**
     * Scope to filter by offer.
     */
    public function scopeByOffer($query, $offerId)
    {
        return $query->where('quotation_offer_id', $offerId);
    }

    /**
     * Scope to filter by accommodation.
     */
    public function scopeByAccommodation($query, $accommodationId)
    {
        return $query->where('accommodation_id', $accommodationId);
    }

    /**
     * Scope to filter by city.
     */
    public function scopeByCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }
}
