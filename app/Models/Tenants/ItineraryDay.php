<?php

namespace App\Models\Tenants;

use App\Models\Base\City;
use App\Models\Base\Accommodation;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ItineraryDay extends Model
{
    use HasUuids, BelongsToTenant, HasTranslations;

    protected $fillable = [
        'description',
        'itinerary_id',
        'day_number',
        'current_city_id',
        'accommodation_city_id',
        'accommodation_id',
        'accommodation_star_rating',
        'has_vehicle',
        'has_tour_guide',
        'creator_user_id',
    ];

    protected $casts = [
        'description' => 'array',
        'has_vehicle' => 'boolean',
        'has_tour_guide' => 'boolean',
        'accommodation_star_rating' => 'integer',
        'day_number' => 'integer',
    ];

    protected $translatable = [
        'description',
    ];

    /**
     * Get the itinerary that owns the day.
     */
    public function itinerary(): BelongsTo
    {
        return $this->belongsTo(Itinerary::class);
    }

    /**
     * Get the current city for this day.
     */
    public function currentCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'current_city_id');
    }

    /**
     * Get the accommodation city for this day.
     */
    public function accommodationCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'accommodation_city_id');
    }

    /**
     * Get the accommodation for this day.
     */
    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }

    /**
     * Get the user who created this day.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_user_id');
    }
}
