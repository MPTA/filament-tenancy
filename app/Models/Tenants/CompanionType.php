<?php

namespace App\Models\Tenants;

use App\Models\Base\CompanionCategory;
use App\Models\Base\Currency;
use App\Models\Base\Language;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class CompanionType extends Model
{
    use HasUuids, BelongsToTenant, HasTranslations;

    protected $fillable = [
        'name',
        'tenant_id',
        'native_language_id',
        'speaking_language_id',
        'companion_category_id',
        'per_day_price',
        'half_day_price',
        'per_hour_price',
        'max_hour_per_day',
        'max_hour_half_day',
        'extra_hour_price',
        'currency_id',
        'base_meal_budget',
        'base_accommodation_budget',
        'slug',
    ];

    protected $casts = [
        'name' => 'array',
        'per_day_price' => 'decimal:2',
        'half_day_price' => 'decimal:2',
        'per_hour_price' => 'decimal:2',
        'max_hour_per_day' => 'integer',
        'max_hour_half_day' => 'integer',
        'extra_hour_price' => 'decimal:2',
        'base_meal_budget' => 'decimal:2',
        'base_accommodation_budget' => 'decimal:2',
    ];

    protected $translatable = [
        'name',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the native language for this companion type.
     */
    public function nativeLanguage(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'native_language_id');
    }

    /**
     * Get the speaking language for this companion type.
     */
    public function speakingLanguage(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'speaking_language_id');
    }

    /**
     * Get the companion category that owns this companion type.
     */
    public function companionCategory(): BelongsTo
    {
        return $this->belongsTo(CompanionCategory::class);
    }

    /**
     * Get the currency that owns this companion type.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Scope a query to filter by companion category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('companion_category_id', $categoryId);
    }

    /**
     * Scope a query to filter by native language.
     */
    public function scopeByNativeLanguage($query, $languageId)
    {
        return $query->where('native_language_id', $languageId);
    }

    /**
     * Scope a query to filter by speaking language.
     */
    public function scopeBySpeakingLanguage($query, $languageId)
    {
        return $query->where('speaking_language_id', $languageId);
    }

    /**
     * Scope a query to filter by price range.
     */
    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('per_day_price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope a query to search companion types.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name->en', 'like', "%{$search}%")
              ->orWhere('name->fa', 'like', "%{$search}%")
              ->orWhere('slug', 'like', "%{$search}%");
        });
    }

    /**
     * Get the formatted per day price.
     */
    public function getFormattedPerDayPriceAttribute(): string
    {
        if (!$this->per_day_price) {
            return 'Not specified';
        }
        $currency = $this->currency ? $this->currency->code : 'USD';
        return number_format((float) $this->per_day_price, 2) . ' ' . $currency;
    }

    /**
     * Get the formatted half day price.
     */
    public function getFormattedHalfDayPriceAttribute(): string
    {
        if (!$this->half_day_price) {
            return 'Not specified';
        }
        $currency = $this->currency ? $this->currency->code : 'USD';
        return number_format((float) $this->half_day_price, 2) . ' ' . $currency;
    }

    /**
     * Get the formatted per hour price.
     */
    public function getFormattedPerHourPriceAttribute(): string
    {
        if (!$this->per_hour_price) {
            return 'Not specified';
        }
        $currency = $this->currency ? $this->currency->code : 'USD';
        return number_format((float) $this->per_hour_price, 2) . ' ' . $currency;
    }

    /**
     * Get the formatted extra hour price.
     */
    public function getFormattedExtraHourPriceAttribute(): string
    {
        if (!$this->extra_hour_price) {
            return 'Not specified';
        }
        $currency = $this->currency ? $this->currency->code : 'USD';
        return number_format((float) $this->extra_hour_price, 2) . ' ' . $currency;
    }

    /**
     * Get the formatted base meal budget.
     */
    public function getFormattedBaseMealBudgetAttribute(): string
    {
        if (!$this->base_meal_budget) {
            return 'Not specified';
        }
        $currency = $this->currency ? $this->currency->code : 'USD';
        return number_format((float) $this->base_meal_budget, 2) . ' ' . $currency;
    }

    /**
     * Get the formatted base accommodation budget.
     */
    public function getFormattedBaseAccommodationBudgetAttribute(): string
    {
        if (!$this->base_accommodation_budget) {
            return 'Not specified';
        }
        $currency = $this->currency ? $this->currency->code : 'USD';
        return number_format((float) $this->base_accommodation_budget, 2) . ' ' . $currency;
    }

    /**
     * Get the quotation offer companions using this companion type.
     */
    public function quotationOfferCompanions(): HasMany
    {
        return $this->hasMany(QuotationOfferCompanion::class);
    }
}
