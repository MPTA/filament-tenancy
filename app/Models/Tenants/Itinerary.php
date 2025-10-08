<?php

namespace App\Models\Tenants;

use App\Enums\StarRatingEnum;
use App\Enums\TravelModeEnum;
use App\Models\Base\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Itinerary extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'itineraryable_id',
        'itineraryable_type',
        'tenant_id',
        'travel_mode',
        'is_advanced',
        'is_complete',
        'is_vip',
        'creator_user_id',
    ];

    protected $casts = [
        'travel_mode' => TravelModeEnum::class,
        'is_advanced' => 'boolean',
        'is_complete' => 'boolean',
        'is_vip' => 'boolean',
        'accommodation_star_rating' => StarRatingEnum::class,
    ];

    protected static function booted(): void
    {
        // When itinerary itself is updated, mark as incomplete and regenerate breakdown
        static::updating(function ($itinerary) {
            if ($itinerary->isDirty(['travel_mode', 'is_advanced', 'is_vip'])) {
                $itinerary->is_complete = false;
                
                // Also mark breakdown as incomplete if it exists
                if ($itinerary->breakdown) {
                    $itinerary->breakdown->update(['is_completed' => false]);
                }
            }
        });

        // When itinerary is updated, regenerate breakdown if it exists
        static::updated(function ($itinerary) {
            if ($itinerary->wasChanged(['travel_mode', 'is_advanced', 'is_vip'])) {
            }
        });

        // When itinerary is deleted, delete the breakdown as well
        static::deleting(function ($itinerary) {
            if ($itinerary->breakdown) {
                try {
                    $itinerary->breakdown->delete();
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to delete breakdown after itinerary deletion: ' . $e->getMessage());
                }
            }
        });
    }

    /**
     * Get the parent itineraryable model (polymorphic relationship).
     */
    public function itineraryable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the breakdown for this itinerary (through itineraryable).
     */
    public function getBreakdownAttribute()
    {
        if ($this->itineraryable instanceof \App\Models\Tenants\QuotationItinerary) {
            return $this->itineraryable->breakdown;
        }
        return null;
    }

    /**
     * Get the creator user.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_user_id');
    }

    /**
     * Get the tenant.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(\Stancl\Tenancy\Database\Models\Tenant::class);
    }

    /**
     * Get the itinerary days.
     */
    public function days(): HasMany
    {
        return $this->hasMany(ItineraryDay::class);
    }

    /**
     * Get the itinerary days with all related data for form editing.
     */
    public function daysForForm(): HasMany
    {
        return $this->hasMany(ItineraryDay::class)
            ->with([
                'currentCity',
                'accommodationCity', 
                'accommodation',
                'activities.meal.mealType',
                'activities.ticket.toCity',
                'activities.attraction.attraction',
                'activities.attraction.subAttractions.subAttraction',
                'activities.experience.experience'
            ])
            ->orderBy('day_number');
    }

    /**
     * Scope to load itinerary with all form data in one query.
     */
    public function scopeForFormEdit($query)
    {
        return $query->with([
            'daysForForm' => function ($query) {
                $query->with([
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
                ])->orderBy('day_number');
            }
        ]);
    }

    public function currenctCity(){
        return $this->belongsTo(City::class, 'current_city_id');    
    }

    public function accommodationCity(){
        return $this->belongsTo(City::class, 'accommodation_city_id');    
    }

    /**
     * Get itinerary with all form data optimized for editing.
     */
    public static function getForFormEdit($id): ?self
    {
        return static::forFormEdit()->find($id);
    }

    /**
     * Get formatted days data for form editing.
     */
    public function getFormattedDaysData(): array
    {
        return $this->daysForForm->map(function ($day) {
            $formattedData = $day->formatted_data;
            
            // Add has_vehicle based on vehicle fields for backward compatibility
            if ((isset($formattedData['vehicle_usage_mode']) && !empty($formattedData['vehicle_usage_mode'])) || 
                (isset($formattedData['vehicle_hours']) && !empty($formattedData['vehicle_hours']))) {
                $formattedData['has_vehicle'] = true;
            } else {
                $formattedData['has_vehicle'] = false;
            }
            
            return $formattedData;
        })->toArray();
    }
}
