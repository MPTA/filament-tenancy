<?php

namespace App\Models\Tenants;

use App\Models\Base\Accommodation;
use App\Models\Base\City;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class InquiryStayPlan extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'inquiry_itinerary_id',
        'stay_city_id',
        'nights',
        'accommodation_stars',
        'accommodation_id',
        'tenant_id',
    ];

    protected $casts = [
        'nights' => 'integer',
        'accommodation_stars' => 'integer',
    ];

    /**
     * Get the inquiry itinerary that owns this stay plan.
     */
    public function inquiryItinerary(): BelongsTo
    {
        return $this->belongsTo(InquiryItinerary::class);
    }

    /**
     * Get the stay city for this stay plan.
     */
    public function stayCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'stay_city_id');
    }

    /**
     * Get the accommodation for this stay plan.
     */
    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }

    /**
     * Scope a query to filter by nights.
     */
    public function scopeByNights($query, $nights)
    {
        return $query->where('nights', $nights);
    }

    /**
     * Scope a query to filter by minimum nights.
     */
    public function scopeMinNights($query, $minNights)
    {
        return $query->where('nights', '>=', $minNights);
    }

    /**
     * Scope a query to filter by maximum nights.
     */
    public function scopeMaxNights($query, $maxNights)
    {
        return $query->where('nights', '<=', $maxNights);
    }

    /**
     * Scope a query to filter by accommodation stars.
     */
    public function scopeByAccommodationStars($query, $stars)
    {
        return $query->where('accommodation_stars', $stars);
    }

    /**
     * Scope a query to filter by minimum accommodation stars.
     */
    public function scopeMinAccommodationStars($query, $minStars)
    {
        return $query->where('accommodation_stars', '>=', $minStars);
    }

    /**
     * Scope a query to filter by stay city.
     */
    public function scopeByStayCity($query, $cityId)
    {
        return $query->where('stay_city_id', $cityId);
    }

    /**
     * Scope a query to filter by accommodation.
     */
    public function scopeByAccommodation($query, $accommodationId)
    {
        return $query->where('accommodation_id', $accommodationId);
    }

    /**
     * Scope a query to search stay plans.
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereHas('stayCity', function ($q) use ($search) {
            $q->where('name->en', 'like', "%{$search}%")
              ->orWhere('name->fa', 'like', "%{$search}%");
        })->orWhereHas('accommodation', function ($q) use ($search) {
            $q->where('name->en', 'like', "%{$search}%")
              ->orWhere('name->fa', 'like', "%{$search}%");
        });
    }

    /**
     * Get the accommodation stars display.
     */
    public function getAccommodationStarsDisplayAttribute(): string
    {
        return str_repeat('★', $this->accommodation_stars) . ' (' . $this->accommodation_stars . ' stars)';
    }

    /**
     * Get the nights display.
     */
    public function getNightsDisplayAttribute(): string
    {
        return $this->nights . ' ' . ($this->nights === 1 ? 'night' : 'nights');
    }
}