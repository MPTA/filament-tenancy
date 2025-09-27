<?php

namespace App\Models\Tenants;

use App\Enums\ChargeModeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class BreakdownExperience extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'breakdown_id',
        'tenant_id',
        'experience_id',
        'price',
        'charge_mode',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'charge_mode' => ChargeModeEnum::class,
        ];
    }

    protected static function booted(): void
    {
        // When breakdown experience is updated, mark parent breakdown as incomplete
        static::updating(function ($experience) {
            if ($experience->isDirty()) {
                $experience->breakdown()->update(['is_completed' => false]);
            }
        });

        // When breakdown experience is created, mark parent breakdown as incomplete
        static::created(function ($experience) {
            $experience->breakdown()->update(['is_completed' => false]);
        });

        // When breakdown experience is deleted, mark parent breakdown as incomplete
        static::deleted(function ($experience) {
            $experience->breakdown()->update(['is_completed' => false]);
        });
    }

    /**
     * Get the breakdown that owns this experience pricing.
     */
    public function breakdown(): BelongsTo
    {
        return $this->belongsTo(Breakdown::class);
    }

    /**
     * Get the experience associated with this pricing.
     */
    public function experience(): BelongsTo
    {
        return $this->belongsTo(Experience::class);
    }
}
