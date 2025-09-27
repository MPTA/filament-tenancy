<?php

namespace App\Models\Tenants;

use App\Enums\MealPartEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ItineraryDayActivityMeal extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'itinerary_day_activity_id',
        'meal_type_id',
        'meal_part',
        'is_included_with_hotel',
        'tenant_id',
    ];

    protected $casts = [
        'meal_part' => MealPartEnum::class,
        'is_included_with_hotel' => 'boolean',
    ];

    protected static function booted(): void
    {
        // When meal is updated, mark parent itinerary and breakdown as incomplete
        static::updating(function ($meal) {
            if ($meal->isDirty()) {
                $meal->itineraryDayActivity->itineraryDay->itinerary()->update(['is_complete' => false]);
                
                // Also mark breakdown as incomplete if it exists
                if ($meal->itineraryDayActivity->itineraryDay->itinerary->breakdown) {
                    $meal->itineraryDayActivity->itineraryDay->itinerary->breakdown->update(['is_completed' => false]);
                }
            }
        });

        // When meal is created, mark parent itinerary and breakdown as incomplete
        static::created(function ($meal) {
            $meal->itineraryDayActivity->itineraryDay->itinerary()->update(['is_complete' => false]);
            
            // Also mark breakdown as incomplete if it exists
            if ($meal->itineraryDayActivity->itineraryDay->itinerary->breakdown) {
                $meal->itineraryDayActivity->itineraryDay->itinerary->breakdown->update(['is_completed' => false]);
            }
        });

        // When meal is deleted, mark parent itinerary and breakdown as incomplete
        static::deleted(function ($meal) {
            $meal->itineraryDayActivity->itineraryDay->itinerary()->update(['is_complete' => false]);
            
            // Also mark breakdown as incomplete if it exists
            if ($meal->itineraryDayActivity->itineraryDay->itinerary->breakdown) {
                $meal->itineraryDayActivity->itineraryDay->itinerary->breakdown->update(['is_completed' => false]);
            }
        });

        // Regenerate breakdown after meal changes
        static::updated(function ($meal) {
            if ($meal->wasChanged()) {
                // Regenerate breakdown if it exists
                if ($meal->itineraryDayActivity->itineraryDay->itinerary->breakdown && $meal->itineraryDayActivity->itineraryDay->itinerary->itineraryable instanceof QuotationItinerary) {
                    try {
                        $meal->itineraryDayActivity->itineraryDay->itinerary->itineraryable->generateBreakdownFromItinerary();
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to regenerate breakdown after meal update: ' . $e->getMessage());
                    }
                }
            }
        });

        static::created(function ($meal) {
            // Regenerate breakdown if it exists
            if ($meal->itineraryDayActivity->itineraryDay->itinerary->breakdown && $meal->itineraryDayActivity->itineraryDay->itinerary->itineraryable instanceof QuotationItinerary) {
                try {
                    $meal->itineraryDayActivity->itineraryDay->itinerary->itineraryable->generateBreakdownFromItinerary();
                } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to regenerate breakdown after meal creation: ' . $e->getMessage());
                }
            }
        });

        static::deleted(function ($meal) {
            // Regenerate breakdown if it exists
            if ($meal->itineraryDayActivity && $meal->itineraryDayActivity->itineraryDay && $meal->itineraryDayActivity->itineraryDay->itinerary && $meal->itineraryDayActivity->itineraryDay->itinerary->breakdown && $meal->itineraryDayActivity->itineraryDay->itinerary->itineraryable instanceof QuotationItinerary) {
                try {
                    $meal->itineraryDayActivity->itineraryDay->itinerary->itineraryable->generateBreakdownFromItinerary();
                } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to regenerate breakdown after meal deletion: ' . $e->getMessage());
                }
            }
        });
    }

    /**
     * Get the itinerary day activity that owns the meal.
     */
    public function itineraryDayActivity(): BelongsTo
    {
        return $this->belongsTo(ItineraryDayActivity::class);
    }

    /**
     * Get the meal type for this meal.
     */
    public function mealType(): BelongsTo
    {
        return $this->belongsTo(MealType::class);
    }

    /**
     * Scope a query to filter by meal part.
     */
    public function scopeByMealPart($query, MealPartEnum $mealPart)
    {
        return $query->where('meal_part', $mealPart);
    }

    /**
     * Scope a query to filter by hotel inclusion.
     */
    public function scopeIncludedWithHotel($query)
    {
        return $query->where('is_included_with_hotel', true);
    }

    /**
     * Scope a query to filter by not included with hotel.
     */
    public function scopeNotIncludedWithHotel($query)
    {
        return $query->where('is_included_with_hotel', false);
    }

    /**
     * Scope a query to filter by meal type.
     */
    public function scopeByMealType($query, $mealTypeId)
    {
        return $query->where('meal_type_id', $mealTypeId);
    }
}
