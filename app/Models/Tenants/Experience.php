<?php

namespace App\Models\Tenants;

use App\Enums\ChargeModeEnum;
use App\Models\Base\City;
use App\Models\Base\Currency;
use App\Models\Base\District;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Experience extends Model
{
    use HasUuids, BelongsToTenant, HasTranslations;

    protected $fillable = [
        'name',
        'description',
        'content',
        'slug',
        'price',
        'charge_mode',
        'currency_id',
        'address',
        'city_id',
        'district_id',
        'is_active',
        'is_free_for_guide',
        'is_free_for_other_companions',
        'creator_user_id',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'content' => 'array',
        'price' => 'decimal:2',
        'charge_mode' => ChargeModeEnum::class,
        'is_active' => 'boolean',
        'is_free_for_guide' => 'boolean',
        'is_free_for_other_companions' => 'boolean',
    ];

    protected $translatable = [
        'name',
        'description',
        'content',
    ];

    /**
     * Get the validation rules for the model.
     */
    public static function validationRules($id = null): array
    {
        return [
            'name' => 'required|array',
            'slug' => 'required|string|unique:experiences,slug,' . $id,
            'city_id' => 'required|uuid|exists:cities,id',
            'district_id' => 'nullable|uuid|exists:districts,id',
            'currency_id' => 'nullable|uuid|exists:currencies,id',
            'price' => 'nullable|numeric|min:0',
            'charge_mode' => 'required|string',
            'is_active' => 'required|boolean',
            'is_free_for_guide' => 'boolean',
            'is_free_for_other_companions' => 'boolean',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|array',
            'content' => 'nullable|array',
            'creator_user_id' => 'required|uuid|exists:users,id',
        ];
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the city that owns the experience.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the district that owns the experience.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get the currency that owns the experience.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the user who created this experience.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_user_id');
    }

    /**
     * Scope a query to only include active experiences.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by charge mode.
     */
    public function scopeByChargeMode($query, $chargeMode)
    {
        return $query->where('charge_mode', $chargeMode);
    }

    /**
     * Scope a query to search experiences.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('address', 'like', "%{$search}%");
        });
    }

    /**
     * Get the formatted price with currency.
     */
    public function getFormattedPriceAttribute(): string
    {
        if (!$this->price) {
            return 'Free';
        }

        $currency = $this->currency ? $this->currency->code : 'USD';
        return number_format((float) $this->price, 2) . ' ' . $currency;
    }

    /**
     * Boot method to automatically set creator_user_id.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($experience) {
            if (empty($experience->creator_user_id)) {
                $experience->creator_user_id = Auth::id();
            }
        });
    }
}
