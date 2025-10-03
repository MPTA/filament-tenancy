<?php

namespace App\Models\Tenants;

use App\Models\Base\City;
use App\Models\Base\RoomCategory;
use App\Models\Base\Accommodation;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOfferGroupCompanionAccommodation extends Model
{
    use HasUuids, BelongsToTenant;

    protected $table = 'quotation_offer_group_companion_accommodations';

    protected $fillable = [
        'quotation_offer_group_companion_id',
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
     * Get the quotation offer group companion that owns this accommodation.
     */
    public function quotationOfferGroupCompanion(): BelongsTo
    {
        return $this->belongsTo(QuotationOfferGroupCompanion::class);
    }

    /**
     * Get the accommodation for this cost.
     */
    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }

    /**
     * Get the room category for this accommodation.
     */
    public function roomCategory(): BelongsTo
    {
        return $this->belongsTo(RoomCategory::class);
    }

    /**
     * Get the city for this accommodation.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the total cost for this accommodation.
     */
    public function getTotalCostAttribute(): float
    {
        return $this->nights * (float) $this->night_price;
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
     * Scope to filter by companion.
     */
    public function scopeByCompanion($query, $companionId)
    {
        return $query->where('quotation_offer_group_companion_id', $companionId);
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

    /**
     * Scope to filter by room category.
     */
    public function scopeByRoomCategory($query, $roomCategoryId)
    {
        return $query->where('room_category_id', $roomCategoryId);
    }

    /**
     * Scope to filter by base budget accommodations.
     */
    public function scopeBaseBudget($query)
    {
        return $query->where('is_base_budget', true);
    }

    /**
     * Scope to filter by specific accommodations (non-base budget).
     */
    public function scopeSpecificAccommodation($query)
    {
        return $query->where('is_base_budget', false);
    }
}
