<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class City extends Model
{
    use HasTranslations, CentralConnection, HasUuids;

    protected $table = 'cities';
    public $translatable = ['name'];

    protected $fillable = ['name', 'code', 'province_id'];

    protected $casts = [
        'name' => 'array',
    ];

    /**
     * Get the province that owns the city.
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * Get the districts for the city.
     */
    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    /**
     * Get the attractions for the city.
     */
    public function attractions(): HasMany
    {
        return $this->hasMany(Attraction::class);
    }

    /**
     * Get the accommodations for the city.
     */
    public function accommodations(): HasMany
    {
        return $this->hasMany(Accommodation::class);
    }

    /**
     * Get cached select options for cities.
     */
    public static function getCachedSelectOptions()
    {
        static $cache = null;
        
        if ($cache === null) {
            $cache = static::query()
                ->select('id', 'name')
                ->orderBy('name')
                ->get()
                ->pluck('name', 'id');
        }
        
        return $cache;
    }

    /**
     * Clear the static cache.
     */
    public static function clearStaticCache()
    {
        static $cache = null;
        $cache = null;
    }
}
