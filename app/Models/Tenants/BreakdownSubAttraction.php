<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class BreakdownSubAttraction extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'breakdown_attraction_id',
        'tenant_id',
        'sub_attraction_id',
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
        // When breakdown sub attraction is updated, mark parent breakdown as incomplete
        static::updating(function ($subAttraction) {
            if ($subAttraction->isDirty()) {
                $subAttraction->breakdownAttraction->breakdown()->update(['is_completed' => false]);
            }
        });

        // When breakdown sub attraction is created, mark parent breakdown as incomplete
        static::created(function ($subAttraction) {
            $subAttraction->breakdownAttraction->breakdown()->update(['is_completed' => false]);
        });

        // When breakdown sub attraction is deleted, mark parent breakdown as incomplete
        static::deleted(function ($subAttraction) {
            $subAttraction->breakdownAttraction->breakdown()->update(['is_completed' => false]);
        });
    }

    /**
     * Get the breakdown attraction that owns this sub-attraction pricing.
     */
    public function breakdownAttraction(): BelongsTo
    {
        return $this->belongsTo(BreakdownAttraction::class);
    }

    /**
     * Get the sub-attraction associated with this pricing.
     */
    public function subAttraction(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Base\SubAttraction::class);
    }
}
