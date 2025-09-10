<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Airport extends Model
{
    use HasUuids, CentralConnection;

    protected $fillable = [
        'name',
        'iata_code',
        'city_id',
    ];

    /**
     * Get the city that owns the airport.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Base\City::class);
    }

    /**
     * Scope a query to search airports by name or IATA code.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('iata_code', 'like', "%{$search}%");
        });
    }

    /**
     * Get the full airport name with IATA code.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->name} ({$this->iata_code})";
    }
}
