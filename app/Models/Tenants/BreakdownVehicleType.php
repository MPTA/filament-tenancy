<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class BreakdownVehicleType extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'breakdown_id',
        'tenant_id',
        'vehicle_type_id',
        'per_day_price',
        'extra_hour_price',
        'half_day_price',
        'airport_transfer_price',
        'empty_back_price',
    ];

    protected function casts(): array
    {
        return [
            'per_day_price' => 'decimal:2',
            'extra_hour_price' => 'decimal:2',
            'half_day_price' => 'decimal:2',
            'airport_transfer_price' => 'decimal:2',
            'empty_back_price' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        // When breakdown vehicle type is updated, mark parent breakdown as incomplete
        static::updating(function ($vehicleType) {
            if ($vehicleType->isDirty()) {
                $vehicleType->breakdown()->update(['is_completed' => false]);
            }
        });

        // When breakdown vehicle type is created, mark parent breakdown as incomplete
        static::created(function ($vehicleType) {
            $vehicleType->breakdown()->update(['is_completed' => false]);
        });

        // When breakdown vehicle type is deleted, mark parent breakdown as incomplete
        static::deleted(function ($vehicleType) {
            $vehicleType->breakdown()->update(['is_completed' => false]);
        });
    }

    /**
     * Get the breakdown that owns this vehicle type pricing.
     */
    public function breakdown(): BelongsTo
    {
        return $this->belongsTo(Breakdown::class);
    }

    /**
     * Get the vehicle type associated with this pricing.
     */
    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }

    /**
     * Check if this vehicle type is used in any offers.
     */
    public function isUsedInOffers(): bool
    {
        $breakdown = $this->breakdown;
        if (!$breakdown || !$breakdown->quotationItinerary) {
            return false;
        }
        
        return QuotationOffer::query()
            ->whereHas('quotationOfferGroup', function ($query) use ($breakdown) {
                $query->where('quotation_itinerary_id', $breakdown->quotation_itinerary_id);
            })
            ->where('vehicle_type_id', $this->vehicle_type_id)
            ->exists();
    }

    /**
     * Get the usage count of this vehicle type in offers.
     */
    public function getUsageCountInOffers(): int
    {
        $breakdown = $this->breakdown;
        if (!$breakdown || !$breakdown->quotationItinerary) {
            return 0;
        }
        
        return QuotationOffer::query()
            ->whereHas('quotationOfferGroup', function ($query) use ($breakdown) {
                $query->where('quotation_itinerary_id', $breakdown->quotation_itinerary_id);
            })
            ->where('vehicle_type_id', $this->vehicle_type_id)
            ->count();
    }
}
