<?php

namespace App\Models\Tenants;

use App\Enums\InquiryDateTypeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class InquiryItinerary extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'inquiry_id',
        'date_type',
        'from_date',
        'to_date',
        'accommodation_stars',
        'tenant_id',
    ];

    protected $casts = [
        'date_type' => InquiryDateTypeEnum::class,
        'from_date' => 'date',
        'to_date' => 'date',
        'accommodation_stars' => 'integer',
    ];

    /**
     * Get the inquiry for this inquiry itinerary (one-to-one relationship).
     */
    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }

    /**
     * Get the stay plans for this inquiry itinerary.
     */
    public function stayPlans(): HasMany
    {
        return $this->hasMany(InquiryStayPlan::class);
    }

    /**
     * Scope a query to filter by date type.
     */
    public function scopeByDateType($query, $dateType)
    {
        return $query->where('date_type', $dateType);
    }

    /**
     * Scope a query to filter by accommodation stars.
     */
    public function scopeByAccommodationStars($query, $stars)
    {
        return $query->where('accommodation_stars', $stars);
    }

    /**
     * Scope a query to filter by minimum accommodation stars.
     */
    public function scopeMinAccommodationStars($query, $minStars)
    {
        return $query->where('accommodation_stars', '>=', $minStars);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('from_date', [$startDate, $endDate])
              ->orWhereBetween('to_date', [$startDate, $endDate])
              ->orWhere(function ($subQ) use ($startDate, $endDate) {
                  $subQ->where('from_date', '<=', $startDate)
                       ->where('to_date', '>=', $endDate);
              });
        });
    }

    /**
     * Scope a query to filter fixed date itineraries.
     */
    public function scopeFixedDate($query)
    {
        return $query->where('date_type', InquiryDateTypeEnum::FIXED_DATE);
    }

    /**
     * Scope a query to filter flexible date itineraries.
     */
    public function scopeFlexibleDate($query)
    {
        return $query->where('date_type', InquiryDateTypeEnum::FLEXIBLE_DATE);
    }

    /**
     * Scope a query to filter series itineraries.
     */
    public function scopeSeries($query)
    {
        return $query->where('date_type', InquiryDateTypeEnum::SERIES);
    }

    /**
     * Get the duration in days.
     */
    public function getDurationInDaysAttribute(): ?int
    {
        if (!$this->from_date || !$this->to_date) {
            return null;
        }
        return \Carbon\Carbon::parse($this->from_date)->diffInDays(\Carbon\Carbon::parse($this->to_date)) + 1;
    }

    /**
     * Get the formatted date range.
     */
    public function getFormattedDateRangeAttribute(): string
    {
        if (!$this->from_date && !$this->to_date) {
            return 'No dates specified';
        }

        if ($this->from_date && !$this->to_date) {
            return 'From ' . \Carbon\Carbon::parse($this->from_date)->format('M d, Y');
        }

        if (!$this->from_date && $this->to_date) {
            return 'Until ' . \Carbon\Carbon::parse($this->to_date)->format('M d, Y');
        }

        return \Carbon\Carbon::parse($this->from_date)->format('M d, Y') . ' - ' . \Carbon\Carbon::parse($this->to_date)->format('M d, Y');
    }

    /**
     * Get the accommodation stars display.
     */
    public function getAccommodationStarsDisplayAttribute(): string
    {
        if (!$this->accommodation_stars) {
            return 'Not specified';
        }

        return str_repeat('★', $this->accommodation_stars) . ' (' . $this->accommodation_stars . ' stars)';
    }
}
