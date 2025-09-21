<?php

namespace App\Models\Tenants;

use App\Models\Base\MealCategory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class MealType extends Model
{
    use HasUuids, BelongsToTenant, HasTranslations;

    protected $fillable = [
        'meal_category_id',
        'name',
        'slug',
        'description',
        'price',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'price' => 'decimal:2',
    ];

    protected $translatable = [
        'name',
        'description',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->getTranslation('name', 'en') ?? 'meal-type');
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') && empty($model->slug)) {
                $model->slug = Str::slug($model->getTranslation('name', 'en') ?? 'meal-type');
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the meal category that owns the meal type.
     */
    public function mealCategory(): BelongsTo
    {
        return $this->belongsTo(MealCategory::class);
    }

    /**
     * Get the activity meals for this meal type.
     */
    public function activityMeals(): HasMany
    {
        return $this->hasMany(ItineraryDayActivityMeal::class);
    }

    /**
     * Scope a query to filter by meal category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('meal_category_id', $categoryId);
    }

    /**
     * Scope a query to filter by price range.
     */
    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
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
     * Get the formatted price with currency.
     */
    public function getFormattedPriceAttribute(): string
    {
        if (!$this->price) {
            return 'Not specified';
        }

        return number_format((float) $this->price, 2) . ' USD';
    }
}
