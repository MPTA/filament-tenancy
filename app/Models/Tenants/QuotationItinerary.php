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
        'is_foreigner_passengers',
    ];

    protected $casts = [
        'is_foreigner_passengers' => 'boolean',
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
        
        // Mark breakdown as incomplete when regenerating
        $breakdown->update(['is_completed' => false]);
        
        $this->updateVehicleData($breakdown);
        $this->createBreakdownTickets($breakdown);
        $this->createBreakdownMeals($breakdown);
        $this->createBreakdownExperiences($breakdown);
        $this->createBreakdownAccommodations($breakdown);
        $this->createBreakdownAttractions($breakdown);

        return $breakdown;
    }

    /**
     * Create or update breakdown record
     */
    private function createOrUpdateBreakdown()
    {
        $breakdown = $this->breakdown;
        
        if (!$breakdown) {
            $breakdown = \App\Models\Tenants\Breakdown::create([
                'quotation_itinerary_id' => $this->id,
                'creator_user_id' => \Illuminate\Support\Facades\Auth::id(),
                'currency_id' => Currency::where('code', 'CNY')->first()->id,
            ]);
        }
        
        return $breakdown;
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

        // Get existing tickets with their prices before deleting
        $existingTickets = $breakdown->tickets->mapWithKeys(function ($ticket) {
            $classValue = $ticket->class;
            if ($classValue instanceof \App\Enums\TicketClassEnum) {
                $classValue = $classValue->value;
            }
            $key = $ticket->transport_mode . '_' . $ticket->from_city_id . '_' . $ticket->to_city_id . '_' . ($classValue ?? 'null');
            return [$key => $ticket->price];
        });

        // Delete all existing tickets first
        $breakdown->tickets()->delete();

        $processedTickets = [];

        foreach ($this->itinerary->days as $day) {
            $ticketActivities = $day->activities()
                ->whereHas('activityCategory', function ($query) {
                    $query->where('type', \App\Enums\ActivityCategoryTypeEnum::TICKET->value);
                })
                ->with('ticket.toCity')
                ->get();

            foreach ($ticketActivities as $activity) {
                if ($activity->ticket) {
                    $key = $activity->ticket->transport_mode . '_' . $activity->city_id . '_' . $activity->ticket->to_city_id . '_' . ($activity->ticket->class?->value ?? 'null');
                    
                    // Only process if not already processed
                    if (!isset($processedTickets[$key])) {
                        // Preserve existing price if available, otherwise use null for tickets (no source table)
                        $price = $existingTickets->get($key, null);
                        
                        $breakdown->tickets()->create([
                            'transport_mode' => $activity->ticket->transport_mode,
                            'from_city_id' => $activity->city_id,
                            'to_city_id' => $activity->ticket->to_city_id,
                            'class' => $activity->ticket->class?->value,
                            'price' => $price,
                        ]);

                        $processedTickets[$key] = true;
                    }
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

        // Get existing meals with their prices before deleting
        $existingMeals = $breakdown->meals->mapWithKeys(function ($meal) {
            $key = $meal->meal_type_id;
            return [$key => $meal->price];
        });

        // Delete all existing meals first
        $breakdown->meals()->delete();

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

        // Create new meals
        foreach ($mealCounts as $mealTypeId => $qty) {
            $mealType = \App\Models\Tenants\MealType::find($mealTypeId);
            // Preserve existing price if available, otherwise use meal type default price
            $price = $existingMeals->get($mealTypeId, $mealType?->price ?? 0.00);
            
            $breakdown->meals()->create([
                'meal_type_id' => $mealTypeId,
                'qty' => $qty,
                'price' => $price,
            ]);
        }
    }

    /**
     * Create breakdown experiences from itinerary
     */
    private function createBreakdownExperiences($breakdown)
    {
        if (!$this->itinerary) return;

        // Get existing experiences with their prices before deleting
        $existingExperiences = $breakdown->experiences->mapWithKeys(function ($experience) {
            $key = $experience->experience_id;
            return [$key => [
                'price' => $experience->price,
                'charge_mode' => $experience->charge_mode,
            ]];
        });

        // Delete all existing experiences first
        $breakdown->experiences()->delete();

        $processedExperiences = [];

        foreach ($this->itinerary->days as $day) {
            $experienceActivities = $day->activities()
                ->whereHas('activityCategory', function ($query) {
                    $query->where('type', \App\Enums\ActivityCategoryTypeEnum::EXPERIENCE->value);
                })
                ->with('experience.experience')
                ->get();

            foreach ($experienceActivities as $activity) {
                if ($activity->experience?->experience?->exists) {
                    // Double check that the experience actually exists in the database
                    $experienceExists = \App\Models\Tenants\Experience::where('id', $activity->experience->experience->id)->exists();
                    
                    if ($experienceExists) {
                        $experienceId = $activity->experience->experience->id;
                        
                        // Only process if not already processed
                        if (!isset($processedExperiences[$experienceId])) {
                            $existingData = $existingExperiences->get($experienceId, [
                                'price' => 0.00,
                                'charge_mode' => \App\Enums\ChargeModeEnum::PER_PERSON,
                            ]);

                            // Preserve existing price if available, otherwise use experience default price
                            $price = $existingData['price'] > 0 ? $existingData['price'] : ($activity->experience->experience->price ?? 0.00);
                            
                            $breakdown->experiences()->create([
                                'experience_id' => $experienceId,
                                'price' => $price,
                                'charge_mode' => $existingData['charge_mode'] ?? ($activity->experience->experience->charge_mode ?? \App\Enums\ChargeModeEnum::PER_PERSON),
                            ]);

                            $processedExperiences[$experienceId] = true;
                        }
                    }
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

        // Get existing accommodations with their room prices before deleting
        $existingAccommodations = $breakdown->accommodations->mapWithKeys(function ($accommodation) {
            $key = "{$accommodation->accommodation_id}_{$accommodation->city_id}";
            $rooms = $accommodation->rooms->mapWithKeys(function ($room) {
                return [$room->room_category_id => $room->price];
            });
            return [$key => $rooms];
        });

        // Delete all existing accommodations first
        $breakdown->accommodations()->delete();

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
            $key = "{$accommodationData['accommodation_id']}_{$accommodationData['city_id']}";
            $existingRooms = $existingAccommodations->get($key, collect());

            $breakdownAccommodation = $breakdown->accommodations()->create([
                'accommodation_id' => $accommodationData['accommodation_id'],
                'city_id' => $accommodationData['city_id'],
                'nights_qty' => $accommodationData['nights'],
            ]);

            $this->createDefaultRoomCategories($breakdownAccommodation, $existingRooms);
        }
    }

    /**
     * Create default room categories for accommodation
     */
    private function createDefaultRoomCategories($breakdownAccommodation, $existingRooms = null)
    {
        $twinRoomCategory = RoomCategory::where('category', \App\Enums\RoomCategoryEnum::TWIN->value)->first();
        $singleRoomCategory = RoomCategory::where('category', \App\Enums\RoomCategoryEnum::SINGLE->value)->first();

        if ($twinRoomCategory) {
            $price = $existingRooms ? $existingRooms->get($twinRoomCategory->id, 0.00) : 0.00;
            $breakdownAccommodation->rooms()->create([
                'room_category_id' => $twinRoomCategory->id,
                'price' => $price,
            ]);
        }

        if ($singleRoomCategory) {
            $price = $existingRooms ? $existingRooms->get($singleRoomCategory->id, 0.00) : 0.00;
            $breakdownAccommodation->rooms()->create([
                'room_category_id' => $singleRoomCategory->id,
                'price' => $price,
            ]);
        }
    }

    /**
     * Create breakdown attractions from itinerary
     */
    private function createBreakdownAttractions($breakdown)
    {
        if (!$this->itinerary) return;

        // Get existing attractions with their prices before deleting
        $existingAttractions = $breakdown->attractions->mapWithKeys(function ($attraction) {
            $key = "{$attraction->attraction_id}_{$attraction->city_id}_{$attraction->is_outview}";
            $subAttractions = $attraction->subAttractions->mapWithKeys(function ($subAttraction) {
                return [$subAttraction->sub_attraction_id => $subAttraction->price];
            });
            return [$key => [
                'entry_price' => $attraction->entry_price,
                'sub_attractions' => $subAttractions,
            ]];
        });

        // Delete all existing attractions first
        $breakdown->attractions()->delete();

        foreach ($this->itinerary->days as $day) {
            $attractionActivities = $day->activities()
                ->whereHas('activityCategory', function ($query) {
                    $query->where('type', \App\Enums\ActivityCategoryTypeEnum::ATTRACTION->value);
                })
                ->with(['attraction.attraction.subAttractions'])
                ->get();

            foreach ($attractionActivities as $activity) {
                if ($activity->attraction?->attraction?->exists) {
                    // Double check that the attraction actually exists in the database
                    $attractionExists = \App\Models\Base\Attraction::where('id', $activity->attraction->attraction->id)->exists();
                    
                    if ($attractionExists) {
                        $key = "{$activity->attraction->attraction->id}_{$activity->city_id}_{$activity->attraction->is_outview}";
                        $existingData = $existingAttractions->get($key, [
                            'entry_price' => 0.00,
                            'sub_attractions' => collect(),
                        ]);

                        // Preserve existing price if available, otherwise use passenger type based price
                        $entryPrice = $existingData['entry_price'] > 0 ? $existingData['entry_price'] : (
                            $this->is_foreigner_passengers 
                                ? ($activity->attraction->attraction->foreigner_price ?? $activity->attraction->attraction->entry_price ?? 0.00)
                                : ($activity->attraction->attraction->local_price ?? $activity->attraction->attraction->entry_price ?? 0.00)
                        );

                        $breakdownAttraction = $breakdown->attractions()->create([
                            'attraction_id' => $activity->attraction->attraction->id,
                            'city_id' => $activity->city_id,
                            'is_outview' => $activity->attraction->is_outview ?? false,
                            'entry_price' => $entryPrice,
                        ]);

                        $this->createBreakdownSubAttractions($breakdownAttraction, $activity->attraction->attraction, $existingData['sub_attractions']);
                    }
                }
            }
        }
    }

    /**
     * Create breakdown sub-attractions
     */
    private function createBreakdownSubAttractions($breakdownAttraction, $attraction, $existingSubAttractions = null)
    {
        // Only create sub-attractions if the attraction is NOT outview
        if (!$breakdownAttraction->is_outview && $attraction->subAttractions) {
            foreach ($attraction->subAttractions as $subAttraction) {
                // Preserve existing price if available, otherwise use passenger type based price
                $existingPrice = $existingSubAttractions ? $existingSubAttractions->get($subAttraction->id, 0.00) : 0.00;
                $subAttractionPrice = $existingPrice > 0 ? $existingPrice : (
                    $this->is_foreigner_passengers 
                        ? ($subAttraction->foreigner_price ?? $subAttraction->price ?? 0.00)
                        : ($subAttraction->local_price ?? $subAttraction->price ?? 0.00)
                );

                $breakdownAttraction->subAttractions()->create([
                    'sub_attraction_id' => $subAttraction->id,
                    'price' => $subAttractionPrice,
                ]);
            }
        }
    }

}
