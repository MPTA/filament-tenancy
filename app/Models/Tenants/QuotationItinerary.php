<?php

namespace App\Models\Tenants;

use App\Models\Base\Currency;
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
        // Create or update breakdown record
        $breakdown = $this->breakdown()->firstOrCreate(
            [],
            [
                'creator_user_id' => \Illuminate\Support\Facades\Auth::id(),
                'currency_id' => Currency::where('code', 'CNY')->first()->id,
            ]
        );

        // Calculate vehicle usage from itinerary days
        $vehicleDaysQty = 0;
        $vehicleHalfDaysQty = 0;
        $vehicleHoursQty = 0;

        if ($this->itinerary) {
            $days = $this->itinerary->days;
            
            foreach ($days as $day) {
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

        // Update breakdown with vehicle data and default budgets
        $breakdown->update([
            'vehicle_days_qty' => $vehicleDaysQty,
            'vehicle_half_days_qty' => $vehicleHalfDaysQty,
            'vehicle_hours_qty' => $vehicleHoursQty,
            'driver_base_meal_budget' => 50.00,
            'driver_base_accommodation_budget' => 100.00,
            'companion_base_meal_budget' => 50.00,
            'companion_base_accommodation_budget' => 100.00,
        ]);

        // Create breakdown tickets from itinerary days
        if ($this->itinerary) {
            $days = $this->itinerary->days;
            
            foreach ($days as $day) {
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

        // Create breakdown meals from itinerary days
        if ($this->itinerary) {
            $days = $this->itinerary->days;
            $mealCounts = [];
            
            foreach ($days as $day) {
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

            // Create one record per meal type with total count
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

        // Create breakdown experiences from itinerary days
        if ($this->itinerary) {
            $days = $this->itinerary->days;
            
            foreach ($days as $day) {
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

        return $breakdown;
    }
}
