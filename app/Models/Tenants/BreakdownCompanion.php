<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class BreakdownCompanion extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'breakdown_id',
        'tenant_id',
        'companion_type_id',
        'per_day_price',
        'half_day_price',
        'pickup_price',
        'per_hour_price',
    ];

    protected function casts(): array
    {
        return [
            'per_day_price' => 'decimal:2',
            'half_day_price' => 'decimal:2',
            'pickup_price' => 'decimal:2',
            'per_hour_price' => 'decimal:2',
        ];
    }

    /**
     * Get the breakdown that owns this companion pricing.
     */
    public function breakdown(): BelongsTo
    {
        return $this->belongsTo(Breakdown::class);
    }

    /**
     * Get the companion type associated with this pricing.
     */
    public function companionType(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Tenants\CompanionType::class, 'companion_type_id');
    }
}
