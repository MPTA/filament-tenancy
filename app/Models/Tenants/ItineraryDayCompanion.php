<?php

namespace App\Models\Tenants;

use App\Enums\HireModeEnum;
use App\Models\Base\CompanionCategory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ItineraryDayCompanion extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'itinerary_day_id',
        'companion_category_id',
        'hire_mode',
        'from_time',
        'to_time',
        'tenant_id',
    ];

    protected $casts = [
        'hire_mode' => HireModeEnum::class,
        'from_time' => 'datetime:H:i',
        'to_time' => 'datetime:H:i',
    ];

    /**
     * Get the itinerary day that owns this companion.
     */
    public function itineraryDay(): BelongsTo
    {
        return $this->belongsTo(ItineraryDay::class);
    }

    /**
     * Get the companion category for this companion.
     */
    public function companionCategory(): BelongsTo
    {
        return $this->belongsTo(CompanionCategory::class);
    }


    /**
     * Scope a query to filter by hire mode.
     */
    public function scopeByHireMode($query, $hireMode)
    {
        return $query->where('hire_mode', $hireMode);
    }

    /**
     * Scope a query to filter by companion category.
     */
    public function scopeByCompanionCategory($query, $categoryId)
    {
        return $query->where('companion_category_id', $categoryId);
    }

    /**
     * Scope a query to filter by time range.
     */
    public function scopeByTimeRange($query, $fromTime, $toTime)
    {
        return $query->where(function ($q) use ($fromTime, $toTime) {
            $q->whereBetween('from_time', [$fromTime, $toTime])
              ->orWhereBetween('to_time', [$fromTime, $toTime])
              ->orWhere(function ($subQ) use ($fromTime, $toTime) {
                  $subQ->where('from_time', '<=', $fromTime)
                       ->where('to_time', '>=', $toTime);
              });
        });
    }

    /**
     * Scope a query to filter daily hire companions.
     */
    public function scopeDaily($query)
    {
        return $query->where('hire_mode', HireModeEnum::DAILY);
    }

    /**
     * Scope a query to filter half day hire companions.
     */
    public function scopeHalfDay($query)
    {
        return $query->where('hire_mode', HireModeEnum::HALF_DAY);
    }

    /**
     * Scope a query to filter hourly hire companions.
     */
    public function scopeHourly($query)
    {
        return $query->where('hire_mode', HireModeEnum::HOURLY);
    }

    /**
     * Scope a query to search companions.
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereHas('companionCategory', function ($q) use ($search) {
            $q->where('name->en', 'like', "%{$search}%")
              ->orWhere('name->fa', 'like', "%{$search}%");
        });
    }

    /**
     * Get the formatted time range.
     */
    public function getFormattedTimeRangeAttribute(): string
    {
        if (!$this->from_time && !$this->to_time) {
            return 'No time specified';
        }

        if ($this->from_time && !$this->to_time) {
            return 'From ' . $this->from_time->format('H:i');
        }

        if (!$this->from_time && $this->to_time) {
            return 'Until ' . $this->to_time->format('H:i');
        }

        return $this->from_time->format('H:i') . ' - ' . $this->to_time->format('H:i');
    }

    /**
     * Get the hire mode display.
     */
    public function getHireModeDisplayAttribute(): string
    {
        return $this->hire_mode->label();
    }

    /**
     * Get the duration in hours.
     */
    public function getDurationInHoursAttribute(): ?float
    {
        if (!$this->from_time || !$this->to_time) {
            return null;
        }

        return $this->from_time->diffInHours($this->to_time);
    }

    /**
     * Check if this is a daily hire.
     */
    public function getIsDailyAttribute(): bool
    {
        return $this->hire_mode === HireModeEnum::DAILY;
    }

    /**
     * Check if this is a half day hire.
     */
    public function getIsHalfDayAttribute(): bool
    {
        return $this->hire_mode === HireModeEnum::HALF_DAY;
    }

    /**
     * Check if this is an hourly hire.
     */
    public function getIsHourlyAttribute(): bool
    {
        return $this->hire_mode === HireModeEnum::HOURLY;
    }
}