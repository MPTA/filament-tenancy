<?php

namespace App\Models\Tenants;

use App\Enums\HireModeEnum;
use App\Enums\StarRatingEnum;
use App\Enums\VehicleUsageModeEnum;
use App\Models\Base\City;
use App\Models\Base\Accommodation;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ItineraryDay extends Model
{
    use HasUuids, BelongsToTenant, HasTranslations;

    protected $fillable = [
        'description',
        'itinerary_id',
        'day_number',
        'current_city_id',
        'accommodation_city_id',
        'accommodation_id',
        'accommodation_star_rating',
        'vehicle_usage_mode',
        'vehicle_hours',
        'companion_hire_mode',
        'companion_hours',
        'creator_user_id',
    ];

    protected $casts = [
        'description' => 'array',
        'vehicle_usage_mode' => VehicleUsageModeEnum::class,
        'vehicle_hours' => 'integer',
        'companion_hire_mode' => HireModeEnum::class,
        'companion_hours' => 'integer',
        'accommodation_star_rating' => StarRatingEnum::class,
        'day_number' => 'integer',
    ];

    protected $translatable = [
        'description',
    ];

    protected static function booted(): void
    {
        // When itinerary day is updated, mark parent itinerary as incomplete and regenerate breakdown
        static::updating(function ($itineraryDay) {
            if ($itineraryDay->isDirty()) {
                $itineraryDay->itinerary()->update(['is_complete' => false]);
                
                // Also mark breakdown as incomplete if it exists
                if ($itineraryDay->itinerary->breakdown) {
                    $itineraryDay->itinerary->breakdown->update(['is_completed' => false]);
                }
            }
        });

        // When itinerary day is created, mark parent itinerary as incomplete and regenerate breakdown
        static::created(function ($itineraryDay) {
            $itineraryDay->itinerary()->update(['is_complete' => false]);
            
            // Also mark breakdown as incomplete if it exists
            if ($itineraryDay->itinerary->breakdown) {
                $itineraryDay->itinerary->breakdown->update(['is_completed' => false]);
            }
        });

        // When itinerary day is deleted, mark parent itinerary as incomplete and regenerate breakdown
        static::deleted(function ($itineraryDay) {
            $itineraryDay->itinerary()->update(['is_complete' => false]);
            
            // Also mark breakdown as incomplete if it exists
            if ($itineraryDay->itinerary->breakdown) {
                $itineraryDay->itinerary->breakdown->update(['is_completed' => false]);
            }
        });

        // Regenerate breakdown after itinerary day changes
        static::updated(function ($itineraryDay) {
            if ($itineraryDay->wasChanged()) {
                // Regenerate breakdown if it exists
                if ($itineraryDay->itinerary->breakdown && $itineraryDay->itinerary->itineraryable instanceof QuotationItinerary) {
                    try {
                        $itineraryDay->itinerary->itineraryable->generateBreakdownFromItinerary();
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to regenerate breakdown after itinerary day update: ' . $e->getMessage());
                    }
                }
            }
        });

        static::created(function ($itineraryDay) {
            // Regenerate breakdown if it exists
            if ($itineraryDay->itinerary->breakdown && $itineraryDay->itinerary->itineraryable instanceof QuotationItinerary) {
                try {
                    $itineraryDay->itinerary->itineraryable->generateBreakdownFromItinerary();
                } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to regenerate breakdown after itinerary day creation: ' . $e->getMessage());
                }
            }
        });

        static::deleted(function ($itineraryDay) {
            // Regenerate breakdown if it exists
            if ($itineraryDay->itinerary && $itineraryDay->itinerary->breakdown && $itineraryDay->itinerary->itineraryable instanceof QuotationItinerary) {
                try {
                    $itineraryDay->itinerary->itineraryable->generateBreakdownFromItinerary();
                } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to regenerate breakdown after itinerary day deletion: ' . $e->getMessage());
                }
            }
        });
    }

    protected $appends = [
        'formatted_data',
        'meals_data',
        'attractions_data', 
        'tickets_data',
        'experiences_data'
    ];

    /**
     * Get the itinerary that owns the day.
     */
    public function itinerary(): BelongsTo
    {
        return $this->belongsTo(Itinerary::class);
    }

    /**
     * Get the current city for this day.
     */
    public function currentCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'current_city_id');
    }

    /**
     * Get the accommodation city for this day.
     */
    public function accommodationCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'accommodation_city_id');
    }

    /**
     * Get the accommodation for this day.
     */
    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }

    /**
     * Get the user who created this day.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_user_id');
    }

    /**
     * Get the activities for this day.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(ItineraryDayActivity::class);
    }


    /**
     * Get formatted data for form editing.
     */
    public function getFormattedDataAttribute(): array
    {
        return [
            'current_city_id' => $this->current_city_id,
            'accommodation_city_id' => $this->accommodation_city_id,
            'accommodation_id' => $this->accommodation_id,
            'accommodation_star_rating' => $this->accommodation_star_rating?->value,
            'vehicle_usage_mode' => $this->vehicle_usage_mode?->value,
            'vehicle_hours' => $this->vehicle_hours,
            'has_companion' => $this->companion_hire_mode?->value === 'daily',
            'companion_hire_mode' => $this->companion_hire_mode?->value,
            'companion_hours' => $this->companion_hours,
            'description' => $this->description,
            'breakfast' => $this->meals_data['breakfast'] ?? null,
            'lunch' => $this->meals_data['lunch'] ?? null,
            'dinner' => $this->meals_data['dinner'] ?? null,
            'attractions' => $this->attractions_data,
            'tickets' => $this->tickets_data,
            'experiences' => $this->experiences_data,
        ];
    }


    /**
     * Get meals data formatted for form.
     */
    public function getMealsDataAttribute(): array
    {
        $meals = ['breakfast' => null, 'lunch' => null, 'dinner' => null];
        
        $mealActivities = $this->activities()
            ->whereHas('activityCategory', function ($query) {
                $query->where('type', \App\Enums\ActivityCategoryTypeEnum::MEAL->value);
            })
            ->with('meal.mealType')
            ->get();

        foreach ($mealActivities as $activity) {
            if ($activity->meal) {
                $mealPart = $activity->meal->meal_part->value;
                $meals[$mealPart] = $activity->meal->meal_type_id;
            }
        }

        return $meals;
    }

    /**
     * Get attractions data formatted for form.
     */
    public function getAttractionsDataAttribute(): array
    {
        $attractions = [];
        
        $attractionActivities = $this->activities()
            ->whereHas('activityCategory', function ($query) {
                $query->where('type', \App\Enums\ActivityCategoryTypeEnum::ATTRACTION->value);
            })
            ->with(['attraction.attraction', 'attraction.subAttractions.subAttraction'])
            ->get();

        foreach ($attractionActivities as $activity) {
            if ($activity->attraction) {
                $attractions[] = [
                    'city_id' => $activity->city_id,
                    'attraction_id' => $activity->attraction->attraction_id,
                    'is_outview' => $activity->attraction->is_outview,
                    'sub_attractions' => $activity->attraction->subAttractions->pluck('sub_attraction_id')->toArray(),
                ];
            }
        }

        return $attractions;
    }

    /**
     * Get tickets data formatted for form.
     */
    public function getTicketsDataAttribute(): array
    {
        $tickets = [];
        
        $ticketActivities = $this->activities()
            ->whereHas('activityCategory', function ($query) {
                $query->where('type', \App\Enums\ActivityCategoryTypeEnum::TICKET->value);
            })
            ->with('ticket.toCity')
            ->get();

        foreach ($ticketActivities as $activity) {
            if ($activity->ticket) {
                $tickets[] = [
                    'from_city_id' => $activity->city_id,
                    'to_city_id' => $activity->ticket->to_city_id,
                    'departure_time' => $activity->start_time?->format('H:i'),
                    'arrival_time' => $activity->end_time?->format('H:i'),
                    'class' => $activity->ticket->class?->value,
                    'transport_number' => $activity->ticket->transport_number,
                    'transport_mode' => $activity->ticket->transport_mode,
                ];
            }
        }

        return $tickets;
    }

    /**
     * Get experiences data formatted for form.
     */
    public function getExperiencesDataAttribute(): array
    {
        $experiences = [];
        
        $experienceActivities = $this->activities()
            ->whereHas('activityCategory', function ($query) {
                $query->where('type', \App\Enums\ActivityCategoryTypeEnum::EXPERIENCE->value);
            })
            ->with('experience.experience')
            ->get();

        foreach ($experienceActivities as $activity) {
            if ($activity->experience) {
                $experiences[] = [
                    'city_id' => $activity->city_id,
                    'experience_id' => $activity->experience->experience_id,
                ];
            }
        }

        return $experiences;
    }

    /**
     * Get all form data in one optimized query.
     */
    public function getFormDataOptimized(): array
    {
        // Load all relationships in one query
        $this->load([
            'currentCity',
            'accommodationCity', 
            'accommodation',
            'activities' => function ($query) {
                $query->with([
                    'meal.mealType',
                    'ticket.toCity',
                    'attraction.attraction',
                    'attraction.subAttractions.subAttraction',
                    'experience.experience'
                ]);
            }
        ]);

        return $this->formatted_data;
    }
}
