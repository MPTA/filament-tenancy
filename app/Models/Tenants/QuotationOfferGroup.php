<?php

namespace App\Models\Tenants;

use App\Models\Base\RoomCategory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOfferGroup extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'quotation_itinerary_id',
        'is_include_driver_meal',
        'is_include_driver_hotel',
        'is_driver_stay_same_hotel',
        'is_driver_same_meal',
        'driver_room_category_id',
        'tenant_id',
    ];

    protected $casts = [
        'is_include_driver_meal' => 'boolean',
        'is_include_driver_hotel' => 'boolean',
        'is_driver_stay_same_hotel' => 'boolean',
        'is_driver_same_meal' => 'boolean',
    ];

    /**
     * Get the quotation itinerary for this offer group.
     */
    public function quotationItinerary(): BelongsTo
    {
        return $this->belongsTo(QuotationItinerary::class);
    }


    /**
     * Get the driver room category for this offer group.
     */
    public function driverRoomCategory(): BelongsTo
    {
        return $this->belongsTo(RoomCategory::class, 'driver_room_category_id');
    }

    /**
     * Get the quotation offer companions for this offer group (one-to-many relationship).
     */
    public function quotationOfferCompanions(): HasMany
    {
        return $this->hasMany(QuotationOfferCompanion::class);
    }

    /**
     * Get the quotation offers for this offer group (one-to-many relationship).
     */
    public function quotationOffers(): HasMany
    {
        return $this->hasMany(QuotationOffer::class);
    }

    /**
     * Get the quotation offer group meals for this offer group (one-to-many relationship).
     */
    public function quotationOfferGroupMeals(): HasMany
    {
        return $this->hasMany(QuotationOfferGroupMeal::class);
    }

    /**
     * Get the quotation offer group attractions for this offer group (one-to-many relationship).
     */
    public function quotationOfferGroupAttractions(): HasMany
    {
        return $this->hasMany(QuotationOfferGroupAttraction::class);
    }

    /**
     * Get the quotation offer group tickets for this offer group (one-to-many relationship).
     */
    public function quotationOfferGroupTickets(): HasMany
    {
        return $this->hasMany(QuotationOfferGroupTicket::class);
    }

    /**
     * Scope a query to filter by quotation itinerary.
     */
    public function scopeByQuotationItinerary($query, $quotationItineraryId)
    {
        return $query->where('quotation_itinerary_id', $quotationItineraryId);
    }


    /**
     * Scope a query to filter by include driver meal.
     */
    public function scopeIncludeDriverMeal($query, $value = true)
    {
        return $query->where('is_include_driver_meal', $value);
    }

    /**
     * Scope a query to filter by include driver hotel.
     */
    public function scopeIncludeDriverHotel($query, $value = true)
    {
        return $query->where('is_include_driver_hotel', $value);
    }

    /**
     * Scope a query to filter by driver stay same hotel.
     */
    public function scopeDriverStaySameHotel($query, $value = true)
    {
        return $query->where('is_driver_stay_same_hotel', $value);
    }

    /**
     * Scope a query to filter by driver same meal.
     */
    public function scopeDriverSameMeal($query, $value = true)
    {
        return $query->where('is_driver_same_meal', $value);
    }


    /**
     * Scope a query to filter by driver room category.
     */
    public function scopeByDriverRoomCategory($query, $roomCategoryId)
    {
        return $query->where('driver_room_category_id', $roomCategoryId);
    }


    /**
     * Check if driver meal is included.
     */
    public function getDriverMealIncludedAttribute(): bool
    {
        return $this->is_include_driver_meal;
    }

    /**
     * Check if driver hotel is included.
     */
    public function getDriverHotelIncludedAttribute(): bool
    {
        return $this->is_include_driver_hotel;
    }

    /**
     * Check if driver stays in the same hotel.
     */
    public function getDriverStaysSameHotelAttribute(): bool
    {
        return $this->is_driver_stay_same_hotel;
    }

    /**
     * Check if driver has same meal.
     */
    public function getDriverSameMealAttribute(): bool
    {
        return $this->is_driver_same_meal;
    }


    /**
     * Get the driver room category name.
     */
    public function getDriverRoomCategoryNameAttribute(): ?string
    {
        return $this->driverRoomCategory?->name;
    }
}
