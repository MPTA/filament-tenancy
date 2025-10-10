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
use Illuminate\Support\Facades\Schema;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationItinerary extends Model
{
    use HasUuids, BelongsToTenant;
    
    // Flag to prevent multiple simultaneous breakdown regenerations
    private static $regeneratingBreakdowns = [];

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
     * Boot method to handle cascade deletes.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($quotationItinerary) {
            // Clear cache for quotation itinerary (using direct cache access)
            try {
                $cacheKey = "itinerary_summary_{$quotationItinerary->id}";
                \Illuminate\Support\Facades\Cache::forget($cacheKey);
            } catch (\Exception $e) {
                // Ignore cache errors
            }
            
            // Delete itinerary and its related data
            if ($quotationItinerary->itinerary) {
                $quotationItinerary->itinerary->delete();
            }
            
            // Delete breakdown and its related data
            if ($quotationItinerary->breakdown) {
                $quotationItinerary->breakdown->delete();
            }
        });
    }

    /**
     * Generate breakdown from itinerary data
     */
    public function generateBreakdownFromItinerary()
    {
        // Prevent multiple simultaneous regenerations for the same itinerary
        if (isset(self::$regeneratingBreakdowns[$this->id])) {
            return $this->breakdown;
        }
        
        // Mark this itinerary as being regenerated
        self::$regeneratingBreakdowns[$this->id] = true;
        
        try {
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
        } finally {
            // Always remove the flag, even if an exception occurs
            unset(self::$regeneratingBreakdowns[$this->id]);
        }
    }

    /**
     * Create or update breakdown record
     */
    private function createOrUpdateBreakdown()
    {
        $breakdown = $this->breakdown;
        
        if (!$breakdown) {
            // Get currency from tenant setting (via country accessor)
            $tenantSetting = \App\Models\TenantSetting::firstOrFail();
            
            $breakdown = \App\Models\Tenants\Breakdown::create([
                'quotation_itinerary_id' => $this->id,
                'creator_user_id' => \Illuminate\Support\Facades\Auth::id(),
                'currency_id' => $tenantSetting->currency_id,
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

        // Get base budgets from tenant settings
        $tenantSettings = \App\Models\TenantSetting::first();
        
        $breakdown->update([
            'vehicle_days_qty' => $vehicleDaysQty,
            'vehicle_half_days_qty' => $vehicleHalfDaysQty,
            'vehicle_hours_qty' => $vehicleHoursQty,
            'driver_base_meal_budget' => ($tenantSettings?->driver_meal_base_budget > 0) ? $tenantSettings->driver_meal_base_budget : 50.00,
            'driver_base_accommodation_budget' => ($tenantSettings?->driver_accommodation_base_budget > 0) ? $tenantSettings->driver_accommodation_base_budget : 100.00,
            'companion_base_meal_budget' => ($tenantSettings?->companion_meal_base_budget > 0) ? $tenantSettings->companion_meal_base_budget : 50.00,
            'companion_base_accommodation_budget' => ($tenantSettings?->companion_accommodation_base_budget > 0) ? $tenantSettings->companion_accommodation_base_budget : 100.00,
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
            // Get enum values (they're always enums now due to casting)
            $transportModeValue = $ticket->transport_mode?->value ?? $ticket->transport_mode;
            $classValue = $ticket->class?->value ?? $ticket->class;
            
            $key = $transportModeValue . '_' . $ticket->from_city_id . '_' . $ticket->to_city_id . '_' . ($classValue ?? 'null');
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
                    // Get enum values (they're always enums now due to casting)
                    $transportModeValue = $activity->ticket->transport_mode?->value ?? $activity->ticket->transport_mode;
                    $classValue = $activity->ticket->class?->value ?? $activity->ticket->class;
                    
                    $key = $transportModeValue . '_' . $activity->city_id . '_' . $activity->ticket->to_city_id . '_' . ($classValue ?? 'null');
                    
                    // Only process if not already processed
                    if (!isset($processedTickets[$key])) {
                        // Preserve existing price if available, otherwise use null for tickets (no source table)
                        $price = $existingTickets->get($key, null);
                        
                        $breakdown->tickets()->create([
                            'transport_mode' => $transportModeValue,
                            'from_city_id' => $activity->city_id,
                            'to_city_id' => $activity->ticket->to_city_id,
                            'class' => $classValue,
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
                            // Check if experience exists in breakdown
                            $existingData = $existingExperiences->get($experienceId);
                            
                            // For new experiences, use the Experience model's charge_mode
                            // For existing experiences, preserve their charge_mode
                            if ($existingData) {
                                // Existing experience - preserve existing data
                                $price = $existingData['price'] > 0 ? $existingData['price'] : ($activity->experience->experience->price ?? 0.00);
                                $chargeMode = $existingData['charge_mode'];
                            } else {
                                // New experience - use Experience model defaults
                                $price = $activity->experience->experience->price ?? 0.00;
                                $chargeMode = $activity->experience->experience->charge_mode ?? \App\Enums\ChargeModeEnum::PER_PERSON;
                            }
                            
                            $breakdown->experiences()->create([
                                'experience_id' => $experienceId,
                                'price' => $price,
                                'charge_mode' => $chargeMode,
                                'is_free_for_guide' => $activity->experience->experience->is_free_for_guide ?? false,
                                'is_free_for_other_companions' => $activity->experience->experience->is_free_for_other_companions ?? false,
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

            // Check if accommodation has breakfast
            $accommodation = \App\Models\Base\Accommodation::find($accommodationData['accommodation_id']);
            $hasBreakfast = $accommodation?->has_breakfast ?? true; // Default to true

            $breakdownAccommodation = $breakdown->accommodations()->create([
                'accommodation_id' => $accommodationData['accommodation_id'],
                'city_id' => $accommodationData['city_id'],
                'nights_qty' => $accommodationData['nights'],
                'has_breakfast' => $hasBreakfast,
            ]);

            $this->createDefaultRoomCategories($breakdownAccommodation, $existingRooms, $hasBreakfast);
        }
    }

    /**
     * Create default room categories for accommodation
     */
    private function createDefaultRoomCategories($breakdownAccommodation, $existingRooms = null, $hasBreakfast = true)
    {
        $twinRoomCategory = RoomCategory::where('category', \App\Enums\RoomCategoryEnum::TWIN->value)->first();
        $singleRoomCategory = RoomCategory::where('category', \App\Enums\RoomCategoryEnum::SINGLE->value)->first();

        if ($twinRoomCategory) {
            $price = $this->getRoomCategoryPrice($breakdownAccommodation, $twinRoomCategory, $existingRooms, $breakdownAccommodation->has_breakfast);
            $breakdownAccommodation->rooms()->create([
                'room_category_id' => $twinRoomCategory->id,
                'price' => $price,
            ]);
        }

        if ($singleRoomCategory) {
            $price = $this->getRoomCategoryPrice($breakdownAccommodation, $singleRoomCategory, $existingRooms, $breakdownAccommodation->has_breakfast);
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
                ->with(['attraction.attraction.subAttractions', 'attraction'])
                ->get();

            foreach ($attractionActivities as $activity) {
                if ($activity->attraction?->attraction?->exists) {
                    // Double check that the attraction actually exists in the database
                    $attractionExists = \App\Models\Base\Attraction::where('id', $activity->attraction->attraction->id)->exists();
                    
                    if ($attractionExists) {
                        // Get is_outview from the itinerary_day_activity_attractions table
                        $isOutview = (bool) ($activity->attraction->is_outview ?? false);
                        
                        $key = "{$activity->attraction->attraction->id}_{$activity->city_id}_{$isOutview}";
                        $existingData = $existingAttractions->get($key, [
                            'entry_price' => 0.00,
                            'sub_attractions' => collect(),
                        ]);

                        // Get pricing from tenant-specific tables first, fallback to central tables
                        $entryPrice = $isOutview ? 0.00 : $this->getAttractionEntryPrice($activity->attraction->attraction, $existingData['entry_price']);

                        $breakdownAttraction = $breakdown->attractions()->create([
                            'attraction_id' => $activity->attraction->attraction->id,
                            'city_id' => $activity->city_id,
                            'is_outview' => $isOutview,
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
                // Get pricing from tenant-specific tables first, fallback to central tables
                $subAttractionPrice = $this->getSubAttractionPrice($attraction, $subAttraction, $existingSubAttractions);

                $breakdownAttraction->subAttractions()->create([
                    'sub_attraction_id' => $subAttraction->id,
                    'price' => $subAttractionPrice,
                ]);
            }
        }
    }

    /**
     * Get room category price from tenant-specific table first, fallback to central table
     */
    private function getRoomCategoryPrice($breakdownAccommodation, $roomCategory, $existingRooms = null, $hasBreakfast = true)
    {
        // Preserve existing price if available
        $existingPrice = $existingRooms ? $existingRooms->get($roomCategory->id, 0.00) : 0.00;
        if ($existingPrice > 0) {
            return $existingPrice;
        }

        // Only get prices for rooms with breakfast if hasBreakfast is true
        if (!$hasBreakfast) {
            // If no breakfast, set price to 0 and show notification
            \Filament\Notifications\Notification::make()
                ->warning()
                ->title('Breakfast Alert')
                ->body("Hotel '{$breakdownAccommodation->accommodation->name}' room prices are set to 0 because the hotel doesn't include breakfast. Please check the prices manually.")
                ->persistent()
                ->send();
            return 0.00;
        }

        // Try to get price from tenant-specific table first
        // Priority 1: Try with is_include_breakfast filter
        $tenantAccommodationPrice = \App\Models\Tenants\TenantAccommodationPrice::query()
            ->where('accommodation_id', $breakdownAccommodation->accommodation_id)
            ->where('room_category_id', $roomCategory->id)
            ->where('is_include_breakfast', $hasBreakfast)
            ->where(function ($query) {
                $query->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('valid_to')
                    ->orWhere('valid_to', '>=', now());
            })
            ->orderBy('valid_from', 'desc')
            ->first();

        if ($tenantAccommodationPrice) {
            return $tenantAccommodationPrice->price ?? 0.00;
        }
        
        // Priority 2: Try without breakfast filter (any breakfast status)
        $tenantAccommodationPriceAny = \App\Models\Tenants\TenantAccommodationPrice::query()
            ->where('accommodation_id', $breakdownAccommodation->accommodation_id)
            ->where('room_category_id', $roomCategory->id)
            ->where(function ($query) {
                $query->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('valid_to')
                    ->orWhere('valid_to', '>=', now());
            })
            ->orderBy('valid_from', 'desc')
            ->first();

        if ($tenantAccommodationPriceAny) {
            return $tenantAccommodationPriceAny->price ?? 0.00;
        }

        // Fallback to central table (if it has is_include_breakfast field)
        $centralAccommodationPrice = \App\Models\Base\AccommodationPrice::query()->where('accommodation_id', $breakdownAccommodation->accommodation_id)
            ->where('room_category_id', $roomCategory->id)
            ->where(function ($query) use ($hasBreakfast) {
                // Check if central table has is_include_breakfast field
                if (Schema::hasColumn('accommodation_prices', 'is_include_breakfast')) {
                    $query->where('is_include_breakfast', $hasBreakfast);
                }
            })
            ->where(function ($query) {
                $query->where('valid_from', null)
                    ->orWhere('valid_from', '<=', now());
            })
            ->where(function ($query) {
                $query->where('valid_to', null)
                    ->orWhere('valid_to', '>=', now());
            })
            ->orderBy('valid_from', 'desc')
            ->first();

        return $centralAccommodationPrice?->price ?? 0.00;
    }

    /**
     * Get attraction entry price from tenant-specific table first, fallback to central table
     */
    private function getAttractionEntryPrice($attraction, $existingPrice = 0.00)
    {
        // Preserve existing price if available
        if ($existingPrice > 0) {
            return $existingPrice;
        }

        // Try to get price from tenant-specific table first
        $tenantAttraction = \App\Models\Tenants\TenantAttraction::where('attraction_id', $attraction->id)->first();
        
        if ($tenantAttraction) {
            if ($this->is_foreigner_passengers) {
                return $tenantAttraction->foreigner_price ?? 0.00;
            } else {
                return $tenantAttraction->local_price ?? 0.00;
            }
        }

        // Fallback to central table
        if ($this->is_foreigner_passengers) {
            return $attraction->foreigner_price ?? $attraction->entry_price ?? 0.00;
        } else {
            return $attraction->local_price ?? $attraction->entry_price ?? 0.00;
        }
    }

    /**
     * Get sub-attraction price from tenant-specific table first, fallback to central table
     */
    private function getSubAttractionPrice($attraction, $subAttraction, $existingSubAttractions = null)
    {
        // Preserve existing price if available
        $existingPrice = $existingSubAttractions ? $existingSubAttractions->get($subAttraction->id, 0.00) : 0.00;
        if ($existingPrice > 0) {
            return $existingPrice;
        }

        // Try to get price from tenant-specific table first
        $tenantAttraction = \App\Models\Tenants\TenantAttraction::where('attraction_id', $attraction->id)->first();
        
        if ($tenantAttraction) {
            $tenantSubAttraction = \App\Models\Tenants\TenantSubAttraction::where('tenant_attraction_id', $tenantAttraction->id)
                ->where('sub_attraction_id', $subAttraction->id)
                ->first();
            
            if ($tenantSubAttraction) {
                if ($this->is_foreigner_passengers) {
                    return $tenantSubAttraction->foreigner_price ?? 0.00;
                } else {
                    return $tenantSubAttraction->local_price ?? 0.00;
                }
            }
        }

        // Fallback to central table
        if ($this->is_foreigner_passengers) {
            return $subAttraction->foreigner_price ?? $subAttraction->price ?? 0.00;
        } else {
            return $subAttraction->local_price ?? $subAttraction->price ?? 0.00;
        }
    }

}
