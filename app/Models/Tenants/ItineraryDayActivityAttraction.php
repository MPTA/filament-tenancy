<?php

namespace App\Models\Tenants;

use App\Models\Base\Attraction;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ItineraryDayActivityAttraction extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'itinerary_day_activity_id',
        'attraction_id',
        'is_outview',
        'tenant_id',
    ];

    protected $casts = [
        'is_outview' => 'boolean',
    ];

    /**
     * Get the itinerary day activity that owns this attraction.
     */
    public function itineraryDayActivity(): BelongsTo
    {
        return $this->belongsTo(ItineraryDayActivity::class);
    }

    /**
     * Get the attraction for this activity.
     */
    public function attraction(): BelongsTo
    {
        return $this->belongsTo(Attraction::class);
    }

    /**
     * Get the sub attractions for this activity attraction (one-to-many relationship).
     */
    public function subAttractions(): HasMany
    {
        return $this->hasMany(ItineraryActivitySubAttraction::class);
    }

    /**
     * Scope a query to filter by outview status.
     */
    public function scopeOutview($query)
    {
        return $query->where('is_outview', true);
    }

    /**
     * Scope a query to filter by non-outview status.
     */
    public function scopeNotOutview($query)
    {
        return $query->where('is_outview', false);
    }

    /**
     * Scope a query to filter by attraction.
     */
    public function scopeByAttraction($query, $attractionId)
    {
        return $query->where('attraction_id', $attractionId);
    }

    /**
     * Scope a query to search attractions.
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereHas('attraction', function ($attractionQuery) use ($search) {
            $attractionQuery->where('name->en', 'like', "%{$search}%")
                           ->orWhere('name->fa', 'like', "%{$search}%")
                           ->orWhere('description->en', 'like', "%{$search}%")
                           ->orWhere('description->fa', 'like', "%{$search}%");
        });
    }
}
