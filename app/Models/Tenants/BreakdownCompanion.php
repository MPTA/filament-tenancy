<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class BreakdownCompanion extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'breakdown_id',
        'tenant_id',
        'companion_type_id',
        'per_day_price',
        'half_day_price',
        'pickup_price',
        'per_hour_price',
    ];

    protected function casts(): array
    {
        return [
            'per_day_price' => 'decimal:2',
            'half_day_price' => 'decimal:2',
            'pickup_price' => 'decimal:2',
            'per_hour_price' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        // When breakdown companion is updated, mark parent breakdown as incomplete
        static::updating(function ($companion) {
            if ($companion->isDirty()) {
                $companion->breakdown()->update(['is_completed' => false]);
            }
        });

        // When breakdown companion is created, mark parent breakdown as incomplete
        static::created(function ($companion) {
            $companion->breakdown()->update(['is_completed' => false]);
        });

        // When breakdown companion is deleted, mark parent breakdown as incomplete
        static::deleted(function ($companion) {
            $companion->breakdown()->update(['is_completed' => false]);
        });
    }

    /**
     * Get the breakdown that owns this companion pricing.
     */
    public function breakdown(): BelongsTo
    {
        return $this->belongsTo(Breakdown::class);
    }

    /**
     * Get the companion type associated with this pricing.
     */
    public function companionType(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Tenants\CompanionType::class, 'companion_type_id');
    }

    /**
     * Check if this companion is used in any offer groups.
     */
    public function isUsedInOfferGroups(): bool
    {
        $breakdown = $this->breakdown;
        if (!$breakdown || !$breakdown->quotationItinerary) {
            return false;
        }
        
        return QuotationOfferGroupCompanion::query()
            ->whereHas('quotationOfferGroup', function ($query) use ($breakdown) {
                $query->where('quotation_itinerary_id', $breakdown->quotation_itinerary_id);
            })
            ->where('companion_type_id', $this->companion_type_id)
            ->exists();
    }

    /**
     * Get the usage count of this companion in offer groups.
     */
    public function getUsageCountInOfferGroups(): int
    {
        $breakdown = $this->breakdown;
        if (!$breakdown || !$breakdown->quotationItinerary) {
            return 0;
        }
        
        return QuotationOfferGroupCompanion::query()
            ->whereHas('quotationOfferGroup', function ($query) use ($breakdown) {
                $query->where('quotation_itinerary_id', $breakdown->quotation_itinerary_id);
            })
            ->where('companion_type_id', $this->companion_type_id)
            ->count();
    }
}
