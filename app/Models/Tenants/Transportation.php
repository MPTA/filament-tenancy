<?php

namespace App\Models\Tenants;

use App\Enums\TransportModeEnum;
use App\Models\Base\BorderPoint;
use App\Models\Base\City;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Transportation extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'departure_date',
        'arrival_date',
        'departure_time',
        'arrival_time',
        'from_city_id',
        'to_city_id',
        'transport_number',
        'transport_mode',
        'entry_border_id',
        'exit_border_id',
        'departure_airport_terminal',
        'arrival_airport_terminal',
        'transportable_id',
        'transportable_type',
        'tenant_id',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'arrival_date' => 'date',
        'departure_time' => 'datetime:H:i',
        'arrival_time' => 'datetime:H:i',
        'transport_mode' => TransportModeEnum::class,
    ];

    /**
     * Get the transportable model (polymorphic relation).
     */
    public function transportable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the departure city.
     */
    public function fromCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'from_city_id');
    }

    /**
     * Get the arrival city.
     */
    public function toCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'to_city_id');
    }

    /**
     * Get the entry border point.
     */
    public function entryBorder(): BelongsTo
    {
        return $this->belongsTo(BorderPoint::class, 'entry_border_id');
    }

    /**
     * Get the exit border point.
     */
    public function exitBorder(): BelongsTo
    {
        return $this->belongsTo(BorderPoint::class, 'exit_border_id');
    }

    /**
     * Scope a query to filter by transport mode.
     */
    public function scopeByTransportMode($query, $transportMode)
    {
        return $query->where('transport_mode', $transportMode);
    }

    /**
     * Scope a query to filter by departure city.
     */
    public function scopeFromCity($query, $cityId)
    {
        return $query->where('from_city_id', $cityId);
    }

    /**
     * Scope a query to filter by arrival city.
     */
    public function scopeToCity($query, $cityId)
    {
        return $query->where('to_city_id', $cityId);
    }

    /**
     * Scope a query to filter by transport number.
     */
    public function scopeByTransportNumber($query, $transportNumber)
    {
        return $query->where('transport_number', $transportNumber);
    }

    /**
     * Scope a query to filter by departure date.
     */
    public function scopeByDepartureDate($query, $date)
    {
        return $query->where('departure_date', $date);
    }

    /**
     * Scope a query to filter by arrival date.
     */
    public function scopeByArrivalDate($query, $date)
    {
        return $query->where('arrival_date', $date);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('departure_date', [$startDate, $endDate])
              ->orWhereBetween('arrival_date', [$startDate, $endDate]);
        });
    }

    /**
     * Scope a query to filter by transportable type.
     */
    public function scopeByTransportableType($query, $type)
    {
        return $query->where('transportable_type', $type);
    }

    /**
     * Scope a query to search transportations.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('transport_number', 'like', "%{$search}%")
              ->orWhereHas('fromCity', function ($cityQuery) use ($search) {
                  $cityQuery->where('name->en', 'like', "%{$search}%")
                           ->orWhere('name->fa', 'like', "%{$search}%");
              })
              ->orWhereHas('toCity', function ($cityQuery) use ($search) {
                  $cityQuery->where('name->en', 'like', "%{$search}%")
                           ->orWhere('name->fa', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Get the formatted departure datetime.
     */
    public function getFormattedDepartureDatetimeAttribute(): string
    {
        if (!$this->departure_date) {
            return 'Not specified';
        }

        $date = $this->departure_date->format('M d, Y');
        $time = $this->departure_time ? $this->departure_time->format('H:i') : '';

        return $time ? "{$date} at {$time}" : $date;
    }

    /**
     * Get the formatted arrival datetime.
     */
    public function getFormattedArrivalDatetimeAttribute(): string
    {
        if (!$this->arrival_date) {
            return 'Not specified';
        }

        $date = $this->arrival_date->format('M d, Y');
        $time = $this->arrival_time ? $this->arrival_time->format('H:i') : '';

        return $time ? "{$date} at {$time}" : $date;
    }

    /**
     * Get the route description.
     */
    public function getRouteDescriptionAttribute(): string
    {
        $fromCity = $this->fromCity ? $this->fromCity->name : 'Unknown';
        $toCity = $this->toCity ? $this->toCity->name : 'Unknown';

        return "{$fromCity} → {$toCity}";
    }

    /**
     * Get the transport mode display.
     */
    public function getTransportModeDisplayAttribute(): string
    {
        return $this->transport_mode->label();
    }
}