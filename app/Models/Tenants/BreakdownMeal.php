<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class BreakdownMeal extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'breakdown_id',
        'tenant_id',
        'meal_type_id',
        'qty',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'price' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        // When breakdown meal is updated, mark parent breakdown as incomplete
        static::updating(function ($meal) {
            if ($meal->isDirty()) {
                $meal->breakdown()->update(['is_completed' => false]);
            }
        });

        // When breakdown meal is created, mark parent breakdown as incomplete
        static::created(function ($meal) {
            $meal->breakdown()->update(['is_completed' => false]);
        });

        // When breakdown meal is deleted, mark parent breakdown as incomplete
        static::deleted(function ($meal) {
            $meal->breakdown()->update(['is_completed' => false]);
        });
    }

    /**
     * Get the breakdown that owns this meal pricing.
     */
    public function breakdown(): BelongsTo
    {
        return $this->belongsTo(Breakdown::class);
    }

    /**
     * Get the meal type associated with this pricing.
     */
    public function mealType(): BelongsTo
    {
        return $this->belongsTo(MealType::class);
    }
}
