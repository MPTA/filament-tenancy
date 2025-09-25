<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Breakdown extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'quotation_itinerary_id',
        'tenant_id',
        'creator_user_id',
        'currency_id',
        'vehicle_days_qty',
        'vehicle_half_days_qty',
        'vehicle_airport_transfers_qty',
        'vehicle_hours_qty',
        'vehicle_empty_backs_qty',
        'driver_base_meal_budget',
        'driver_base_accommodation_budget',
        'companion_base_meal_budget',
        'companion_base_accommodation_budget',
    ];

    protected function casts(): array
    {
        return [
            'vehicle_days_qty' => 'integer',
            'vehicle_half_days_qty' => 'integer',
            'vehicle_airport_transfers_qty' => 'integer',
            'vehicle_hours_qty' => 'integer',
            'vehicle_empty_backs_qty' => 'integer',
            'driver_base_meal_budget' => 'decimal:2',
            'driver_base_accommodation_budget' => 'decimal:2',
            'companion_base_meal_budget' => 'decimal:2',
            'companion_base_accommodation_budget' => 'decimal:2',
        ];
    }

    /**
     * Get the quotation itinerary that owns the breakdown.
     */
    public function quotationItinerary(): BelongsTo
    {
        return $this->belongsTo(QuotationItinerary::class, 'quotation_itinerary_id');
    }

    /**
     * Get the user who created this breakdown.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'creator_user_id');
    }

    /**
     * Get the vehicle types with pricing for this breakdown.
     */
    public function vehicleTypes(): HasMany
    {
        return $this->hasMany(BreakdownVehicleType::class);
    }

    /**
     * Get the companions with pricing for this breakdown.
     */
    public function companions(): HasMany
    {
        return $this->hasMany(BreakdownCompanion::class);
    }

    /**
     * Get the tickets with pricing for this breakdown.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(BreakdownTicket::class);
    }

    /**
     * Get the meals with pricing for this breakdown.
     */
    public function meals(): HasMany
    {
        return $this->hasMany(BreakdownMeal::class);
    }

    /**
     * Get the experiences with pricing for this breakdown.
     */
    public function experiences(): HasMany
    {
        return $this->hasMany(BreakdownExperience::class);
    }

    /**
     * Get the expenses for this breakdown.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(BreakdownExpense::class);
    }

    /**
     * Get the accommodations for this breakdown.
     */
    public function accommodations(): HasMany
    {
        return $this->hasMany(BreakdownAccommodation::class);
    }

    /**
     * Get the attractions for this breakdown.
     */
    public function attractions(): HasMany
    {
        return $this->hasMany(BreakdownAttraction::class);
    }
}
