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
}
