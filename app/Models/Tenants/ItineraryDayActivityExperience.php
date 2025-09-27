<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ItineraryDayActivityExperience extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'itinerary_day_activity_id',
        'experience_id',
        'tenant_id',
    ];

    protected static function booted(): void
    {
        // When experience is updated, mark parent itinerary and breakdown as incomplete
        static::updating(function ($experience) {
            if ($experience->isDirty()) {
                $experience->itineraryDayActivity->itineraryDay->itinerary()->update(['is_complete' => false]);
                
                // Also mark breakdown as incomplete if it exists
                if ($experience->itineraryDayActivity->itineraryDay->itinerary->breakdown) {
                    $experience->itineraryDayActivity->itineraryDay->itinerary->breakdown->update(['is_completed' => false]);
                }
            }
        });

        // When experience is created, mark parent itinerary and breakdown as incomplete
        static::created(function ($experience) {
            $experience->itineraryDayActivity->itineraryDay->itinerary()->update(['is_complete' => false]);
            
            // Also mark breakdown as incomplete if it exists
            if ($experience->itineraryDayActivity->itineraryDay->itinerary->breakdown) {
                $experience->itineraryDayActivity->itineraryDay->itinerary->breakdown->update(['is_completed' => false]);
            }
        });

        // When experience is deleted, mark parent itinerary and breakdown as incomplete
        static::deleted(function ($experience) {
            $experience->itineraryDayActivity->itineraryDay->itinerary()->update(['is_complete' => false]);
            
            // Also mark breakdown as incomplete if it exists
            if ($experience->itineraryDayActivity->itineraryDay->itinerary->breakdown) {
                $experience->itineraryDayActivity->itineraryDay->itinerary->breakdown->update(['is_completed' => false]);
            }
        });

        // Regenerate breakdown after experience changes
        static::updated(function ($experience) {
            if ($experience->wasChanged()) {
                // Regenerate breakdown if it exists
                if ($experience->itineraryDayActivity->itineraryDay->itinerary->breakdown && $experience->itineraryDayActivity->itineraryDay->itinerary->itineraryable instanceof QuotationItinerary) {
                    try {
                        $experience->itineraryDayActivity->itineraryDay->itinerary->itineraryable->generateBreakdownFromItinerary();
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to regenerate breakdown after experience update: ' . $e->getMessage());
                    }
                }
            }
        });

        static::created(function ($experience) {
            // Regenerate breakdown if it exists
            if ($experience->itineraryDayActivity->itineraryDay->itinerary->breakdown && $experience->itineraryDayActivity->itineraryDay->itinerary->itineraryable instanceof QuotationItinerary) {
                try {
                    $experience->itineraryDayActivity->itineraryDay->itinerary->itineraryable->generateBreakdownFromItinerary();
                } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to regenerate breakdown after experience creation: ' . $e->getMessage());
                }
            }
        });

        static::deleted(function ($experience) {
            // Regenerate breakdown if it exists
            if ($experience->itineraryDayActivity && $experience->itineraryDayActivity->itineraryDay && $experience->itineraryDayActivity->itineraryDay->itinerary && $experience->itineraryDayActivity->itineraryDay->itinerary->breakdown && $experience->itineraryDayActivity->itineraryDay->itinerary->itineraryable instanceof QuotationItinerary) {
                try {
                    $experience->itineraryDayActivity->itineraryDay->itinerary->itineraryable->generateBreakdownFromItinerary();
                } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to regenerate breakdown after experience deletion: ' . $e->getMessage());
                }
            }
        });
    }

    /**
     * Get the itinerary day activity that owns this experience.
     */
    public function itineraryDayActivity(): BelongsTo
    {
        return $this->belongsTo(ItineraryDayActivity::class);
    }

    /**
     * Get the experience for this activity.
     */
    public function experience(): BelongsTo
    {
        return $this->belongsTo(Experience::class);
    }

    /**
     * Scope a query to filter by experience.
     */
    public function scopeByExperience($query, $experienceId)
    {
        return $query->where('experience_id', $experienceId);
    }

    /**
     * Scope a query to filter by activity.
     */
    public function scopeByActivity($query, $activityId)
    {
        return $query->where('itinerary_day_activity_id', $activityId);
    }

    /**
     * Scope a query to search experiences.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('description->en', 'like', "%{$search}%")
              ->orWhere('description->fa', 'like', "%{$search}%")
              ->orWhereHas('experience', function ($experienceQuery) use ($search) {
                  $experienceQuery->where('name->en', 'like', "%{$search}%")
                                 ->orWhere('name->fa', 'like', "%{$search}%")
                                 ->orWhere('description->en', 'like', "%{$search}%")
                                 ->orWhere('description->fa', 'like', "%{$search}%");
              });
        });
    }
}
