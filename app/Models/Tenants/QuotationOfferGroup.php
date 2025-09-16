<?php

namespace App\Models\Tenants;

use App\Models\Base\RoomCategory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOfferGroup extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'quotation_itinerary_id',
        'is_companion_stay_same_hotel',
        'companion_room_category_id',
        'is_include_driver_cost',
        'is_driver_stay_same_hotel',
        'driver_room_category_id',
        'tenant_id',
    ];

    protected $casts = [
        'is_companion_stay_same_hotel' => 'boolean',
        'is_include_driver_cost' => 'boolean',
        'is_driver_stay_same_hotel' => 'boolean',
    ];

    /**
     * Get the quotation itinerary for this offer group.
     */
    public function quotationItinerary(): BelongsTo
    {
        return $this->belongsTo(QuotationItinerary::class);
    }

    /**
     * Get the companion room category for this offer group.
     */
    public function companionRoomCategory(): BelongsTo
    {
        return $this->belongsTo(RoomCategory::class, 'companion_room_category_id');
    }

    /**
     * Get the driver room category for this offer group.
     */
    public function driverRoomCategory(): BelongsTo
    {
        return $this->belongsTo(RoomCategory::class, 'driver_room_category_id');
    }

    /**
     * Scope a query to filter by quotation itinerary.
     */
    public function scopeByQuotationItinerary($query, $quotationItineraryId)
    {
        return $query->where('quotation_itinerary_id', $quotationItineraryId);
    }

    /**
     * Scope a query to filter by companion stay same hotel.
     */
    public function scopeCompanionStaySameHotel($query, $value = true)
    {
        return $query->where('is_companion_stay_same_hotel', $value);
    }

    /**
     * Scope a query to filter by include driver cost.
     */
    public function scopeIncludeDriverCost($query, $value = true)
    {
        return $query->where('is_include_driver_cost', $value);
    }

    /**
     * Scope a query to filter by driver stay same hotel.
     */
    public function scopeDriverStaySameHotel($query, $value = true)
    {
        return $query->where('is_driver_stay_same_hotel', $value);
    }

    /**
     * Scope a query to filter by companion room category.
     */
    public function scopeByCompanionRoomCategory($query, $roomCategoryId)
    {
        return $query->where('companion_room_category_id', $roomCategoryId);
    }

    /**
     * Scope a query to filter by driver room category.
     */
    public function scopeByDriverRoomCategory($query, $roomCategoryId)
    {
        return $query->where('driver_room_category_id', $roomCategoryId);
    }

    /**
     * Check if companion stays in the same hotel.
     */
    public function getCompanionStaysSameHotelAttribute(): bool
    {
        return $this->is_companion_stay_same_hotel;
    }

    /**
     * Check if driver cost is included.
     */
    public function getDriverCostIncludedAttribute(): bool
    {
        return $this->is_include_driver_cost;
    }

    /**
     * Check if driver stays in the same hotel.
     */
    public function getDriverStaysSameHotelAttribute(): bool
    {
        return $this->is_driver_stay_same_hotel;
    }

    /**
     * Get the companion room category name.
     */
    public function getCompanionRoomCategoryNameAttribute(): ?string
    {
        return $this->companionRoomCategory?->name;
    }

    /**
     * Get the driver room category name.
     */
    public function getDriverRoomCategoryNameAttribute(): ?string
    {
        return $this->driverRoomCategory?->name;
    }
}
