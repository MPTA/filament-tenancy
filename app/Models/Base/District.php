<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class District extends Model
{
    use HasTranslations, CentralConnection, HasUuids;

    protected $table = 'districts';
    public $translatable = ['name'];

    protected $fillable = [
        'name',
        'city_id',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the city that owns the district.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the attractions for the district.
     */
    public function attractions(): HasMany
    {
        return $this->hasMany(Attraction::class);
    }

    /**
     * Get the accommodations for the district.
     */
    public function accommodations(): HasMany
    {
        return $this->hasMany(Accommodation::class);
    }
}
