<?php

namespace App\Models\Tenants;

use App\Models\Base\ActivityCategory;
use App\Models\Base\City;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ItineraryDayActivity extends Model
{
    use HasUuids, BelongsToTenant, HasTranslations;

    protected $fillable = [
        'itinerary_day_id',
        'activity_category_id',
        'start_time',
        'end_time',
        'city_id',
        'description',
        'tenant_id',
    ];

    protected $casts = [
        'description' => 'array',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    protected $translatable = [
        'description',
    ];

    /**
     * Get the itinerary day that owns the activity.
     */
    public function itineraryDay(): BelongsTo
    {
        return $this->belongsTo(ItineraryDay::class);
    }

    /**
     * Get the activity category for this activity.
     */
    public function activityCategory(): BelongsTo
    {
        return $this->belongsTo(ActivityCategory::class);
    }

    /**
     * Get the city for this activity.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the meal for this activity (one-to-one relationship).
     */
    public function meal(): HasOne
    {
        return $this->hasOne(ItineraryDayActivityMeal::class);
    }

    /**
     * Get the ticket for this activity (one-to-one relationship).
     */
    public function ticket(): HasOne
    {
        return $this->hasOne(ItineraryDayActivityTicket::class);
    }

    /**
     * Get the attraction for this activity (one-to-one relationship).
     */
    public function attraction(): HasOne
    {
        return $this->hasOne(ItineraryDayActivityAttraction::class);
    }

    /**
     * Scope a query to filter by activity category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('activity_category_id', $categoryId);
    }

    /**
     * Scope a query to filter by city.
     */
    public function scopeByCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    /**
     * Scope a query to filter by time range.
     */
    public function scopeByTimeRange($query, $startTime, $endTime)
    {
        return $query->whereBetween('start_time', [$startTime, $endTime]);
    }

    /**
     * Scope a query to search activities.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('description', 'like', "%{$search}%");
        });
    }

    /**
     * Get the duration of the activity in minutes.
     */
    public function getDurationInMinutesAttribute(): int
    {
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        
        return $start->diffInMinutes($end);
    }

    /**
     * Get the duration of the activity in hours.
     */
    public function getDurationInHoursAttribute(): float
    {
        return round($this->duration_in_minutes / 60, 2);
    }

    /**
     * Get the formatted duration.
     */
    public function getFormattedDurationAttribute(): string
    {
        $hours = floor($this->duration_in_minutes / 60);
        $minutes = $this->duration_in_minutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}m";
        }
    }
}
