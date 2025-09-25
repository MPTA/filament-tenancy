<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class BreakdownAccommodation extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'breakdown_id',
        'tenant_id',
        'accommodation_id',
        'city_id',
        'nights_qty',
    ];

    protected function casts(): array
    {
        return [
            'nights_qty' => 'integer',
        ];
    }

    /**
     * Get the breakdown that owns this accommodation.
     */
    public function breakdown(): BelongsTo
    {
        return $this->belongsTo(Breakdown::class);
    }

    /**
     * Get the accommodation associated with this breakdown.
     */
    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Base\Accommodation::class);
    }

    /**
     * Get the city for this accommodation.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Base\City::class);
    }

    /**
     * Get the rooms with pricing for this breakdown accommodation.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(BreakdownAccommodationRoom::class);
    }
}
