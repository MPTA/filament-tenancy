<?php

namespace App\Models\Tenants;

use App\Models\Base\MealCategory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class MealType extends Model
{
    use HasUuids, BelongsToTenant, HasTranslations;

    protected $fillable = [
        'meal_category_id',
        'name',
        'description',
        'budget',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'budget' => 'decimal:2',
    ];

    protected $translatable = [
        'name',
        'description',
    ];

    /**
     * Get the meal category that owns the meal type.
     */
    public function mealCategory(): BelongsTo
    {
        return $this->belongsTo(MealCategory::class);
    }

    /**
     * Scope a query to filter by meal category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('meal_category_id', $categoryId);
    }

    /**
     * Scope a query to filter by budget range.
     */
    public function scopeByBudgetRange($query, $minBudget, $maxBudget)
    {
        return $query->whereBetween('budget', [$minBudget, $maxBudget]);
    }

    /**
     * Scope a query to search meal types.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Get the formatted budget with currency.
     */
    public function getFormattedBudgetAttribute(): string
    {
        if (!$this->budget) {
            return 'Not specified';
        }

        return number_format($this->budget, 2) . ' USD';
    }
}
