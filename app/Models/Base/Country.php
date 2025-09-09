<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Country extends Model
{
    use HasTranslations, CentralConnection, HasUuids;

    protected $table = 'countries';
    public $translatable = ['name'];

    protected $fillable = ['name', 'code'];

    protected $casts = [
        'name' => 'array',
    ];

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
