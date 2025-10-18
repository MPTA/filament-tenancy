<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Country extends Model
{
    use HasTranslations, CentralConnection, HasUuids;

    protected $table = 'countries';
    public $translatable = ['name'];

    protected $fillable = [
        'name',
        'code',
        'currency_id',
        'iso3',
        'numeric_code',
        'phone_code',
        'capital',
        'tld',
        'native_name',
        'population',
        'gdp',
        'nationality',
        'timezones',
        'latitude',
        'longitude',
        'emoji',
        'emoji_u',
        'wiki_data_id',
    ];

    protected $casts = [
        'name' => 'array',
        'timezones' => 'array',
        'population' => 'integer',
        'gdp' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the currency for the country.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the provinces for the country.
     */
    public function provinces(): HasMany
    {
        return $this->hasMany(Province::class);
    }

    /**
     * Get the cities for the country through provinces.
     */
    public function cities(): HasManyThrough
    {
        return $this->hasManyThrough(City::class, Province::class);
    }

    /**
     * Get the attractions for the country.
     */
    public function attractions(): HasMany
    {
        return $this->hasMany(Attraction::class);
    }

    /**
     * Get the accommodations for the country.
     */
    public function accommodations(): HasMany
    {
        return $this->hasMany(Accommodation::class);
    }
}
