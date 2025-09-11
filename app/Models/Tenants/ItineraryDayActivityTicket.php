<?php

namespace App\Models\Tenants;

use App\Enums\TicketClassEnum;
use App\Models\Base\City;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ItineraryDayActivityTicket extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'itinerary_day_activity_id',
        'to_city_id',
        'class',
        'transport_number',
        'transport_mode',
        'tenant_id',
    ];

    protected $casts = [
        'class' => TicketClassEnum::class,
    ];

    /**
     * Get the itinerary day activity that owns this ticket.
     */
    public function itineraryDayActivity(): BelongsTo
    {
        return $this->belongsTo(ItineraryDayActivity::class);
    }

    /**
     * Get the destination city for this ticket.
     */
    public function toCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'to_city_id');
    }

    /**
     * Scope a query to filter by ticket class.
     */
    public function scopeByClass($query, $class)
    {
        return $query->where('class', $class);
    }

    /**
     * Scope a query to filter by transport mode.
     */
    public function scopeByTransportMode($query, $mode)
    {
        return $query->where('transport_mode', $mode);
    }

    /**
     * Scope a query to filter by destination city.
     */
    public function scopeToCity($query, $cityId)
    {
        return $query->where('to_city_id', $cityId);
    }

    /**
     * Scope a query to search tickets.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('transport_number', 'like', "%{$search}%")
              ->orWhere('transport_mode', 'like', "%{$search}%")
              ->orWhereHas('toCity', function ($cityQuery) use ($search) {
                  $cityQuery->where('name->en', 'like', "%{$search}%")
                           ->orWhere('name->fa', 'like', "%{$search}%");
              });
        });
    }
}
