<?php

namespace App\Models\Tenants;

use App\Models\Base\SubAttraction;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ItineraryActivitySubAttraction extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'itinerary_day_activity_attraction_id',
        'sub_attraction_id',
        'tenant_id',
    ];

    protected static function booted(): void
    {
        // When sub attraction is updated, mark parent itinerary and breakdown as incomplete
        static::updating(function ($subAttraction) {
            if ($subAttraction->isDirty()) {
                $subAttraction->itineraryDayActivityAttraction->itineraryDayActivity->itineraryDay->itinerary()->update(['is_complete' => false]);
                
                // Also mark breakdown as incomplete if it exists
                if ($subAttraction->itineraryDayActivityAttraction->itineraryDayActivity->itineraryDay->itinerary->breakdown) {
                    $subAttraction->itineraryDayActivityAttraction->itineraryDayActivity->itineraryDay->itinerary->breakdown->update(['is_completed' => false]);
                }
            }
        });

        // When sub attraction is created, mark parent itinerary and breakdown as incomplete
        static::created(function ($subAttraction) {
            $subAttraction->itineraryDayActivityAttraction->itineraryDayActivity->itineraryDay->itinerary()->update(['is_complete' => false]);
            
            // Also mark breakdown as incomplete if it exists
            if ($subAttraction->itineraryDayActivityAttraction->itineraryDayActivity->itineraryDay->itinerary->breakdown) {
                $subAttraction->itineraryDayActivityAttraction->itineraryDayActivity->itineraryDay->itinerary->breakdown->update(['is_completed' => false]);
            }
        });

        // When sub attraction is deleted, mark parent itinerary and breakdown as incomplete
        static::deleted(function ($subAttraction) {
            $subAttraction->itineraryDayActivityAttraction->itineraryDayActivity->itineraryDay->itinerary()->update(['is_complete' => false]);
            
            // Also mark breakdown as incomplete if it exists
            if ($subAttraction->itineraryDayActivityAttraction->itineraryDayActivity->itineraryDay->itinerary->breakdown) {
                $subAttraction->itineraryDayActivityAttraction->itineraryDayActivity->itineraryDay->itinerary->breakdown->update(['is_completed' => false]);
            }
        });
    }

    /**
     * Get the itinerary day activity attraction that owns this sub attraction.
     */
    public function itineraryDayActivityAttraction(): BelongsTo
    {
        return $this->belongsTo(ItineraryDayActivityAttraction::class);
    }

    /**
     * Get the sub attraction for this activity.
     */
    public function subAttraction(): BelongsTo
    {
        return $this->belongsTo(SubAttraction::class);
    }

    /**
     * Scope a query to filter by sub attraction.
     */
    public function scopeBySubAttraction($query, $subAttractionId)
    {
        return $query->where('sub_attraction_id', $subAttractionId);
    }

    /**
     * Scope a query to filter by activity attraction.
     */
    public function scopeByActivityAttraction($query, $activityAttractionId)
    {
        return $query->where('itinerary_day_activity_attraction_id', $activityAttractionId);
    }

    /**
     * Scope a query to search sub attractions.
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereHas('subAttraction', function ($subAttractionQuery) use ($search) {
            $subAttractionQuery->where('name->en', 'like', "%{$search}%")
                              ->orWhere('name->fa', 'like', "%{$search}%")
                              ->orWhere('description->en', 'like', "%{$search}%")
                              ->orWhere('description->fa', 'like', "%{$search}%");
        });
    }
}
