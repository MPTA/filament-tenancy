<?php

namespace App\Models\Tenants;

use App\Enums\ChargeModeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class BreakdownExpense extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'breakdown_id',
        'tenant_id',
        'description',
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
        // When breakdown expense is updated, mark parent breakdown as incomplete
        static::updating(function ($expense) {
            if ($expense->isDirty()) {
                $expense->breakdown()->update(['is_completed' => false]);
            }
        });

        // When breakdown expense is created, mark parent breakdown as incomplete
        static::created(function ($expense) {
            $expense->breakdown()->update(['is_completed' => false]);
        });

        // When breakdown expense is deleted, mark parent breakdown as incomplete
        static::deleted(function ($expense) {
            $expense->breakdown()->update(['is_completed' => false]);
        });
    }

    /**
     * Get the breakdown that owns this expense.
     */
    public function breakdown(): BelongsTo
    {
        return $this->belongsTo(Breakdown::class);
    }
}
