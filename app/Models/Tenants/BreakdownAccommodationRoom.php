<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class BreakdownAccommodationRoom extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'breakdown_accommodation_id',
        'tenant_id',
        'room_category_id',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        // When breakdown accommodation room is updated, mark parent breakdown as incomplete
        static::updating(function ($room) {
            if ($room->isDirty()) {
                $room->breakdownAccommodation->breakdown()->update(['is_completed' => false]);
            }
        });

        // When breakdown accommodation room is created, mark parent breakdown as incomplete
        static::created(function ($room) {
            $room->breakdownAccommodation->breakdown()->update(['is_completed' => false]);
        });

        // When breakdown accommodation room is deleted, mark parent breakdown as incomplete
        static::deleted(function ($room) {
            $room->breakdownAccommodation->breakdown()->update(['is_completed' => false]);
        });
    }

    /**
     * Get the breakdown accommodation that owns this room pricing.
     */
    public function breakdownAccommodation(): BelongsTo
    {
        return $this->belongsTo(BreakdownAccommodation::class);
    }

    /**
     * Get the room category associated with this pricing.
     */
    public function roomCategory(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Base\RoomCategory::class);
    }
}
