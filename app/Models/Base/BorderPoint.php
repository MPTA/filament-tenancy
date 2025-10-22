<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class BorderPoint extends Model
{
    use HasUuids, CentralConnection, HasTranslations;

    protected $fillable = [
        'name',
        'iata_code',
        'city_id',
    ];

    public $translatable = ['name'];

    protected $casts = [];

    /**
     * Get the city that owns the border point.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Base\City::class);
    }

    /**
     * Scope a query to search border points by name or IATA code.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('iata_code', 'like', "%{$search}%");
        });
    }

    /**
     * Get the full border point name with IATA code.
     */
    public function getFullNameAttribute(): string
    {
        $name = is_array($this->name) ? ($this->name['en'] ?? reset($this->name)) : $this->name;
        return $this->iata_code 
            ? "{$name} ({$this->iata_code})"
            : $name;
    }
}
