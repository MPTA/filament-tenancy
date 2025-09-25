<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class BreakdownAttraction extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'breakdown_id',
        'tenant_id',
        'city_id',
        'attraction_id',
        'is_outview',
        'entry_price',
    ];

    protected function casts(): array
    {
        return [
            'is_outview' => 'boolean',
            'entry_price' => 'decimal:2',
        ];
    }

    /**
     * Get the breakdown that owns this attraction.
     */
    public function breakdown(): BelongsTo
    {
        return $this->belongsTo(Breakdown::class);
    }

    /**
     * Get the city for this attraction.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Base\City::class);
    }

    /**
     * Get the attraction associated with this breakdown.
     */
    public function attraction(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Base\Attraction::class);
    }

    /**
     * Get the sub-attractions with pricing for this breakdown attraction.
     */
    public function subAttractions(): HasMany
    {
        return $this->hasMany(BreakdownSubAttraction::class);
    }
}
