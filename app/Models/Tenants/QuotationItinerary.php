<?php

namespace App\Models\Tenants;

use App\Models\Base\CompanionCategory;
use App\Models\Base\Currency;
use App\Models\Base\RoomCategory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationItinerary extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'quotation_id',
        'tenant_id',
    ];

    /**
     * Get the quotation associated with this itinerary.
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    /**
     * Get the quotation offer groups for this itinerary (one-to-many relationship).
     */
    public function quotationOfferGroups(): HasMany
    {
        return $this->hasMany(QuotationOfferGroup::class);
    }

    public function itinerary(){
        return $this->morphOne(Itinerary::class, 'itineraryable');
    }

    /**
     * Get the breakdown for this quotation itinerary (one-to-one relationship).
     */
    public function breakdown(): HasOne
    {
        return $this->hasOne(Breakdown::class);
    }

    /**
     * Generate breakdown from itinerary data
     */
    public function generateBreakdownFromItinerary()
    {
        $breakdown = $this->createOrUpdateBreakdown();
        $this->updateVehicleData($breakdown);
        $this->createBreakdownTickets($breakdown);
        $this->createBreakdownMeals($breakdown);
        $this->createBreakdownExperiences($breakdown);
        $this->createBreakdownAccommodations($breakdown);
        $this->createBreakdownAttractions($breakdown);
        $this->createBreakdownCompanions($breakdown);

        return $breakdown;
    }

    /**
     * Create or update breakdown record
     */
    private function createOrUpdateBreakdown()
    {
        return $this->breakdown()->firstOrCreate(
            [],
            [
                'creator_user_id' => \Illuminate\Support\Facades\Auth::id(),
                'currency_id' => Currency::where('code', 'CNY')->first()->id,
            ]
        );
    }

    /**
     * Update vehicle data and default budgets
     */
    private function updateVehicleData($breakdown)
    {
        $vehicleDaysQty = 0;
        $vehicleHalfDaysQty = 0;
        $vehicleHoursQty = 0;

        if ($this->itinerary) {
            foreach ($this->itinerary->days as $day) {
                switch ($day->vehicle_usage_mode) {
                    case \App\Enums\VehicleUsageModeEnum::FULL_DAY:
                        $vehicleDaysQty++;
                        break;
                    case \App\Enums\VehicleUsageModeEnum::HALF_DAY:
                        $vehicleHalfDaysQty++;
                        break;
                    case \App\Enums\VehicleUsageModeEnum::HOUR:
                        $vehicleHoursQty += $day->vehicle_hours ?? 0;
                        break;
                }
            }
        }

        $breakdown->update([
            'vehicle_days_qty' => $vehicleDaysQty,
            'vehicle_half_days_qty' => $vehicleHalfDaysQty,
            'vehicle_hours_qty' => $vehicleHoursQty,
            'driver_base_meal_budget' => 50.00,
            'driver_base_accommodation_budget' => 100.00,
            'companion_base_meal_budget' => 50.00,
            'companion_base_accommodation_budget' => 100.00,
        ]);
    }

    /**
     * Create breakdown tickets from itinerary
     */
    private function createBreakdownTickets($breakdown)
    {
        if (!$this->itinerary) return;

        foreach ($this->itinerary->days as $day) {
            $ticketActivities = $day->activities()
                ->whereHas('activityCategory', function ($query) {
                    $query->where('type', \App\Enums\ActivityCategoryTypeEnum::TICKET->value);
                })
                ->with('ticket.toCity')
                ->get();

            foreach ($ticketActivities as $activity) {
                if ($activity->ticket) {
                    $breakdown->tickets()->firstOrCreate([
                        'transport_mode' => $activity->ticket->transport_mode,
                        'from_city_id' => $activity->city_id,
                        'to_city_id' => $activity->ticket->to_city_id,
                        'class' => $activity->ticket->class?->value,
                    ], [
                        'price' => 0.00,
                    ]);
                }
            }
        }
    }

    /**
     * Create breakdown meals from itinerary
     */
    private function createBreakdownMeals($breakdown)
    {
        if (!$this->itinerary) return;

        $mealCounts = [];
        
        foreach ($this->itinerary->days as $day) {
            $mealActivities = $day->activities()
                ->whereHas('activityCategory', function ($query) {
                    $query->where('type', \App\Enums\ActivityCategoryTypeEnum::MEAL->value);
                })
                ->with('meal.mealType')
                ->get();

            foreach ($mealActivities as $activity) {
                if ($activity->meal?->mealType) {
                    $mealTypeId = $activity->meal->meal_type_id;
                    $mealCounts[$mealTypeId] = ($mealCounts[$mealTypeId] ?? 0) + 1;
                }
            }
        }

        foreach ($mealCounts as $mealTypeId => $qty) {
            $mealType = \App\Models\Tenants\MealType::find($mealTypeId);
            $breakdown->meals()->firstOrCreate([
                'meal_type_id' => $mealTypeId,
            ], [
                'qty' => $qty,
                'price' => $mealType?->price ?? 0.00,
            ]);
        }
    }

    /**
     * Create breakdown experiences from itinerary
     */
    private function createBreakdownExperiences($breakdown)
    {
        if (!$this->itinerary) return;

        foreach ($this->itinerary->days as $day) {
            $experienceActivities = $day->activities()
                ->whereHas('activityCategory', function ($query) {
                    $query->where('type', \App\Enums\ActivityCategoryTypeEnum::EXPERIENCE->value);
                })
                ->with('experience')
                ->get();

            foreach ($experienceActivities as $activity) {
                if ($activity->experience) {
                    $breakdown->experiences()->firstOrCreate([
                        'experience_id' => $activity->experience->id,
                    ], [
                        'price' => $activity->experience->price ?? 0.00,
                        'charge_mode' => $activity->experience->charge_mode ?? \App\Enums\ChargeModeEnum::PER_PERSON,
                    ]);
                }
            }
        }
    }

    /**
     * Create breakdown accommodations from itinerary
     */
    private function createBreakdownAccommodations($breakdown)
    {
        if (!$this->itinerary) return;

        $accommodationNights = [];
        
        foreach ($this->itinerary->days as $day) {
            if ($day->accommodation_id && $day->accommodation_city_id) {
                $key = "{$day->accommodation_id}_{$day->accommodation_city_id}";
                
                if (!isset($accommodationNights[$key])) {
                    $accommodationNights[$key] = [
                        'accommodation_id' => $day->accommodation_id,
                        'city_id' => $day->accommodation_city_id,
                        'nights' => 0
                    ];
                }
                $accommodationNights[$key]['nights']++;
            }
        }

        foreach ($accommodationNights as $accommodationData) {
            $breakdownAccommodation = $breakdown->accommodations()->firstOrCreate([
                'accommodation_id' => $accommodationData['accommodation_id'],
                'city_id' => $accommodationData['city_id'],
            ], [
                'nights_qty' => $accommodationData['nights'],
            ]);

            $this->createDefaultRoomCategories($breakdownAccommodation);
        }
    }

    /**
     * Create default room categories for accommodation
     */
    private function createDefaultRoomCategories($breakdownAccommodation)
    {
        $twinRoomCategory = RoomCategory::where('category', \App\Enums\RoomCategoryEnum::TWIN->value)->first();
        $singleRoomCategory = RoomCategory::where('category', \App\Enums\RoomCategoryEnum::SINGLE->value)->first();

        if ($twinRoomCategory) {
            $breakdownAccommodation->rooms()->firstOrCreate([
                'room_category_id' => $twinRoomCategory->id,
            ], [
                'price' => 0.00,
            ]);
        }

        if ($singleRoomCategory) {
            $breakdownAccommodation->rooms()->firstOrCreate([
                'room_category_id' => $singleRoomCategory->id,
            ], [
                'price' => 0.00,
            ]);
        }
    }

    /**
     * Create breakdown attractions from itinerary
     */
    private function createBreakdownAttractions($breakdown)
    {
        if (!$this->itinerary) return;

        foreach ($this->itinerary->days as $day) {
            $attractionActivities = $day->activities()
                ->whereHas('activityCategory', function ($query) {
                    $query->where('type', \App\Enums\ActivityCategoryTypeEnum::ATTRACTION->value);
                })
                ->with(['attraction.subAttractions', 'attractionActivity'])
                ->get();

            foreach ($attractionActivities as $activity) {
                if ($activity->attraction) {
                    $breakdownAttraction = $breakdown->attractions()->firstOrCreate([
                        'attraction_id' => $activity->attraction->id,
                        'city_id' => $activity->city_id,
                    ], [
                        'is_outview' => $activity->attractionActivity?->is_outview ?? false,
                        'entry_price' => $activity->attraction->entry_price ?? 0.00,
                    ]);

                    $this->createBreakdownSubAttractions($breakdownAttraction, $activity->attraction);
                }
            }
        }
    }

    /**
     * Create breakdown sub-attractions
     */
    private function createBreakdownSubAttractions($breakdownAttraction, $attraction)
    {
        if ($attraction->subAttractions) {
            foreach ($attraction->subAttractions as $subAttraction) {
                $breakdownAttraction->subAttractions()->firstOrCreate([
                    'sub_attraction_id' => $subAttraction->id,
                ], [
                    'price' => $subAttraction->price ?? 0.00,
                ]);
            }
        }
    }

    /**
     * Create breakdown companions from itinerary
     */
    private function createBreakdownCompanions($breakdown)
    {
        if (!$this->itinerary) return;

        $companionTypes = [];
        
        foreach ($this->itinerary->days as $day) {
            foreach ($day->companions as $companion) {
                $companionCategoryId = $companion->companion_category_id;
                
                if (!isset($companionTypes[$companionCategoryId])) {
                    $companionTypes[$companionCategoryId] = [
                        'companion_category_id' => $companionCategoryId,
                        'count' => 0
                    ];
                }
                $companionTypes[$companionCategoryId]['count']++;
            }
        }

        foreach ($companionTypes as $companionData) {
            $companionCategory = CompanionCategory::find($companionData['companion_category_id']);
            
            $breakdown->companions()->firstOrCreate([
                'companion_type_id' => $companionData['companion_category_id'],
            ], [
                'per_day_price' => $companionCategory?->per_day_price ?? 0.00,
                'half_day_price' => $companionCategory?->half_day_price ?? 0.00,
                'pickup_price' => $companionCategory?->pickup_price ?? 0.00,
                'per_hour_price' => $companionCategory?->per_hour_price ?? 0.00,
            ]);
        }
    }
}
