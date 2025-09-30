<?php

namespace App\Models\Tenants;

use App\Models\Base\RoomCategory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOffer extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'quotation_offer_group_id',
        'vehicle_type_id',
        'leaders_qty',
        'leader_room_category_id',
        'pax_qty',
        'drivers_qty',
        'markup',
        'vehicle_days_qty',
        'vehicle_half_days_qty',
        'vehicle_airport_transfers_qty',
        'vehicle_day_price',
        'vehicle_half_day_price',
        'vehicle_airport_transfer_price',
        'tenant_id',
    ];

    protected $casts = [
        'leaders_qty' => 'integer',
        'pax_qty' => 'integer',
        'drivers_qty' => 'integer',
        'markup' => 'decimal:2',
        'vehicle_days_qty' => 'integer',
        'vehicle_half_days_qty' => 'integer',
        'vehicle_airport_transfers_qty' => 'integer',
        'vehicle_day_price' => 'decimal:2',
        'vehicle_half_day_price' => 'decimal:2',
        'vehicle_airport_transfer_price' => 'decimal:2',
    ];

    /**
     * Get the quotation offer group for this offer.
     */
    public function quotationOfferGroup(): BelongsTo
    {
        return $this->belongsTo(QuotationOfferGroup::class);
    }

    /**
     * Get the vehicle type for this offer.
     */
    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }

    /**
     * Get the leader room category for this offer.
     */
    public function leaderRoomCategory(): BelongsTo
    {
        return $this->belongsTo(RoomCategory::class, 'leader_room_category_id');
    }

    /**
     * Get the quotation offer prices for this offer (one-to-many relationship).
     */
    public function quotationOfferPrices(): HasMany
    {
        return $this->hasMany(QuotationOfferPrice::class);
    }

    /**
     * Scope a query to filter by quotation offer group.
     */
    public function scopeByQuotationOfferGroup($query, $quotationOfferGroupId)
    {
        return $query->where('quotation_offer_group_id', $quotationOfferGroupId);
    }

    /**
     * Scope a query to filter by vehicle type.
     */
    public function scopeByVehicleType($query, $vehicleTypeId)
    {
        return $query->where('vehicle_type_id', $vehicleTypeId);
    }

    /**
     * Scope a query to filter by leader room category.
     */
    public function scopeByLeaderRoomCategory($query, $roomCategoryId)
    {
        return $query->where('leader_room_category_id', $roomCategoryId);
    }

    /**
     * Scope a query to filter by leaders quantity range.
     */
    public function scopeByLeadersQtyRange($query, $minQty, $maxQty)
    {
        return $query->whereBetween('leaders_qty', [$minQty, $maxQty]);
    }

    /**
     * Scope a query to filter by pax quantity range.
     */
    public function scopeByPaxQtyRange($query, $minQty, $maxQty)
    {
        return $query->whereBetween('pax_qty', [$minQty, $maxQty]);
    }

    /**
     * Scope a query to filter by drivers quantity range.
     */
    public function scopeByDriversQtyRange($query, $minQty, $maxQty)
    {
        return $query->whereBetween('drivers_qty', [$minQty, $maxQty]);
    }

    /**
     * Scope a query to filter by markup range.
     */
    public function scopeByMarkupRange($query, $minMarkup, $maxMarkup)
    {
        return $query->whereBetween('markup', [$minMarkup, $maxMarkup]);
    }


    /**
     * Scope a query to filter by minimum capacity.
     */
    public function scopeByMinCapacity($query, $capacity)
    {
        return $query->whereHas('vehicleType', function ($q) use ($capacity) {
            $q->where('capacity_from', '<=', $capacity)
              ->where('capacity_to', '>=', $capacity);
        });
    }

    /**
     * Get the formatted markup percentage.
     */
    public function getFormattedMarkupAttribute(): string
    {
        return number_format((float) $this->markup, 2) . '%';
    }

    /**
     * Get the markup as decimal (e.g., 0.15 for 15%).
     */
    public function getMarkupDecimalAttribute(): float
    {
        return (float) $this->markup / 100;
    }

    /**
     * Get the vehicle type name.
     */
    public function getVehicleTypeNameAttribute(): ?string
    {
        return $this->vehicleType?->name;
    }

    /**
     * Get the leader room category name.
     */
    public function getLeaderRoomCategoryNameAttribute(): ?string
    {
        return $this->leaderRoomCategory?->name;
    }

    /**
     * Get the total people count (leaders + pax + drivers).
     */
    public function getTotalPeopleAttribute(): int
    {
        return $this->leaders_qty + $this->pax_qty + $this->drivers_qty;
    }

    /**
     * Check if this offer has leaders.
     */
    public function getHasLeadersAttribute(): bool
    {
        return $this->leaders_qty > 0;
    }

    /**
     * Check if this offer has drivers.
     */
    public function getHasDriversAttribute(): bool
    {
        return $this->drivers_qty > 0;
    }

    /**
     * Check if this offer has pax.
     */
    public function getHasPaxAttribute(): bool
    {
        return $this->pax_qty > 0;
    }

    /**
     * Get the vehicle capacity range.
     */
    public function getVehicleCapacityRangeAttribute(): ?string
    {
        return $this->vehicleType?->capacity_range;
    }

    /**
     * Check if the total people fit in the vehicle capacity.
     */
    public function getFitsInVehicleAttribute(): bool
    {
        if (!$this->vehicleType) {
            return false;
        }
        
        $totalPeople = $this->total_people;
        return $totalPeople >= $this->vehicleType->capacity_from && 
               $totalPeople <= $this->vehicleType->capacity_to;
    }

    /**
     * Get capacity utilization percentage.
     */
    public function getCapacityUtilizationAttribute(): float
    {
        if (!$this->vehicleType || $this->vehicleType->capacity_to == 0) {
            return 0;
        }
        
        return ($this->total_people / $this->vehicleType->capacity_to) * 100;
    }

    /**
     * Get formatted capacity utilization.
     */
    public function getFormattedCapacityUtilizationAttribute(): string
    {
        return number_format($this->capacity_utilization, 1) . '%';
    }

    /**
     * Get total vehicle days (full days + half days).
     */
    public function getTotalVehicleDaysAttribute(): float
    {
        return $this->vehicle_days_qty + ($this->vehicle_half_days_qty * 0.5);
    }

    /**
     * Get total vehicle cost.
     */
    public function getTotalVehicleCostAttribute(): float
    {
        return ($this->vehicle_days_qty * (float) $this->vehicle_day_price) +
               ($this->vehicle_half_days_qty * (float) $this->vehicle_half_day_price) +
               ($this->vehicle_airport_transfers_qty * (float) $this->vehicle_airport_transfer_price);
    }

    /**
     * Get formatted total vehicle cost.
     */
    public function getFormattedTotalVehicleCostAttribute(): string
    {
        return number_format($this->total_vehicle_cost, 2);
    }

    /**
     * Get formatted vehicle day price.
     */
    public function getFormattedVehicleDayPriceAttribute(): string
    {
        return number_format((float) $this->vehicle_day_price, 2);
    }

    /**
     * Get formatted vehicle half day price.
     */
    public function getFormattedVehicleHalfDayPriceAttribute(): string
    {
        return number_format((float) $this->vehicle_half_day_price, 2);
    }

    /**
     * Get formatted vehicle airport transfer price.
     */
    public function getFormattedVehicleAirportTransferPriceAttribute(): string
    {
        return number_format((float) $this->vehicle_airport_transfer_price, 2);
    }

    /**
     * Get vehicle pricing breakdown.
     */
    public function getVehiclePricingBreakdownAttribute(): array
    {
        return [
            'days' => [
                'qty' => $this->vehicle_days_qty,
                'price' => (float) $this->vehicle_day_price,
                'total' => $this->vehicle_days_qty * (float) $this->vehicle_day_price,
            ],
            'half_days' => [
                'qty' => $this->vehicle_half_days_qty,
                'price' => (float) $this->vehicle_half_day_price,
                'total' => $this->vehicle_half_days_qty * (float) $this->vehicle_half_day_price,
            ],
            'airport_transfers' => [
                'qty' => $this->vehicle_airport_transfers_qty,
                'price' => (float) $this->vehicle_airport_transfer_price,
                'total' => $this->vehicle_airport_transfers_qty * (float) $this->vehicle_airport_transfer_price,
            ],
            'total_cost' => $this->total_vehicle_cost,
            'total_days' => $this->total_vehicle_days,
        ];
    }

    /**
     * Scope a query to filter by vehicle days quantity range.
     */
    public function scopeByVehicleDaysQtyRange($query, $minQty, $maxQty)
    {
        return $query->whereBetween('vehicle_days_qty', [$minQty, $maxQty]);
    }

    /**
     * Scope a query to filter by vehicle half days quantity range.
     */
    public function scopeByVehicleHalfDaysQtyRange($query, $minQty, $maxQty)
    {
        return $query->whereBetween('vehicle_half_days_qty', [$minQty, $maxQty]);
    }

    /**
     * Scope a query to filter by vehicle airport transfers quantity range.
     */
    public function scopeByVehicleAirportTransfersQtyRange($query, $minQty, $maxQty)
    {
        return $query->whereBetween('vehicle_airport_transfers_qty', [$minQty, $maxQty]);
    }

    /**
     * Check if this offer has vehicle costs.
     */
    public function getHasVehicleCostsAttribute(): bool
    {
        return $this->total_vehicle_cost > 0;
    }
}

