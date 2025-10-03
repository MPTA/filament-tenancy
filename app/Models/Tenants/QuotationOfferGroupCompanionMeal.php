<?php

namespace App\Models\Tenants;

use App\Models\Tenants\MealType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOfferGroupCompanionMeal extends Model
{
    use HasUuids, BelongsToTenant;

    protected $table = 'quotation_offer_group_companion_meals';

    protected $fillable = [
        'quotation_offer_group_companion_id',
        'meal_type_id',
        'qty',
        'price',
        'is_base_budget',
        'tenant_id',
    ];

    protected $casts = [
        'qty' => 'integer',
        'price' => 'decimal:2',
        'is_base_budget' => 'boolean',
    ];

    /**
     * Get the quotation offer group companion that owns this meal.
     */
    public function quotationOfferGroupCompanion(): BelongsTo
    {
        return $this->belongsTo(QuotationOfferGroupCompanion::class);
    }

    /**
     * Get the meal type for this meal.
     */
    public function mealType(): BelongsTo
    {
        return $this->belongsTo(MealType::class);
    }

    /**
     * Get the total cost for this meal entry.
     */
    public function getTotalCostAttribute(): float
    {
        return $this->qty * $this->price;
    }

    /**
     * Get formatted total cost.
     */
    public function getFormattedTotalCostAttribute(): string
    {
        return number_format($this->total_cost, 2);
    }

    /**
     * Scope to filter by base budget meals.
     */
    public function scopeBaseBudget($query)
    {
        return $query->where('is_base_budget', true);
    }

    /**
     * Scope to filter by specific meal types.
     */
    public function scopeByMealType($query, $mealTypeId)
    {
        return $query->where('meal_type_id', $mealTypeId);
    }

    /**
     * Scope to filter by companion.
     */
    public function scopeByCompanion($query, $companionId)
    {
        return $query->where('quotation_offer_group_companion_id', $companionId);
    }
}
