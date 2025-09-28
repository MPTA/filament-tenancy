<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class TenantAccommodationPrice extends Model
{
    use HasUuids, BelongsToTenant;

    /**
     * The table associated with the model.
     */
    protected $table = 'tenant_accommodation_prices';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id',
        'tenant_id',
        'accommodation_id',
        'room_category_id',
        'price',
        'currency_id',
        'valid_from',
        'valid_to',
        'creator_user_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'valid_from' => 'date',
        'valid_to' => 'date',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Auto-set creator_user_id from authenticated user
            if (empty($model->creator_user_id)) {
                $model->creator_user_id = \Illuminate\Support\Facades\Auth::id();
            }
            
            // Set default valid_from to current date if not provided
            if (empty($model->valid_from)) {
                $model->valid_from = Carbon::now()->toDateString();
            }
        });
    }

    /**
     * Get the accommodation that owns the price.
     */
    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Base\Accommodation::class);
    }

    /**
     * Get the room category that owns the price.
     */
    public function roomCategory(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Base\RoomCategory::class);
    }

    /**
     * Get the currency that owns the price.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Base\Currency::class);
    }

    /**
     * Get the user that created the price.
     */
    public function creatorUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'creator_user_id');
    }

    /**
     * Scope a query to only include prices valid for a given date.
     */
    public function scopeValidForDate($query, ?Carbon $date = null)
    {
        $date ??= Carbon::now();
        
        return $query->where('valid_from', '<=', $date)
                    ->where(function ($q) use ($date) {
                        $q->whereNull('valid_to')
                          ->orWhere('valid_to', '>=', $date);
                    });
    }

    /**
     * Scope a query to only include prices for a specific accommodation.
     */
    public function scopeForAccommodation($query, $accommodationId)
    {
        return $query->where('accommodation_id', $accommodationId);
    }

    /**
     * Scope a query to only include prices for a specific room category.
     */
    public function scopeForRoomCategory($query, $roomCategoryId)
    {
        return $query->where('room_category_id', $roomCategoryId);
    }

    /**
     * Get the current valid price for accommodation and room category.
     */
    public static function getCurrentPrice($accommodationId, $roomCategoryId, ?Carbon $date = null)
    {
        return static::validForDate($date)
                    ->forAccommodation($accommodationId)
                    ->forRoomCategory($roomCategoryId)
                    ->orderBy('valid_from', 'desc')
                    ->first();
    }

    /**
     * Check if the price is currently valid.
     */
    public function isValidForDate(?Carbon $date = null): bool
    {
        $date ??= Carbon::now();
        
        return $this->valid_from <= $date && 
               ($this->valid_to === null || $this->valid_to >= $date);
    }


    /**
     * Scope a query to only include prices created by a specific user.
     */
    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('creator_user_id', $userId);
    }

    /**
     * Scope a query to only include active prices (currently valid).
     */
    public function scopeActive($query, ?Carbon $date = null)
    {
        return $query->validForDate($date);
    }

    /**
     * Get all valid prices for accommodation and room category.
     */
    public static function getValidPrices($accommodationId, $roomCategoryId, ?Carbon $date = null)
    {
        return static::validForDate($date)
                    ->forAccommodation($accommodationId)
                    ->forRoomCategory($roomCategoryId)
                    ->orderBy('valid_from', 'desc')
                    ->get();
    }

    /**
     * Check if there's an overlapping price period.
     */
    public function hasOverlappingPeriod(): bool
    {
        $query = static::where('accommodation_id', $this->accommodation_id)
                      ->where('room_category_id', $this->room_category_id)
                      ->where('tenant_id', $this->tenant_id);

        if ($this->exists) {
            $query->where('id', '!=', $this->id);
        }

        return $query->where(function ($q) {
            $q->where(function ($subQ) {
                // Check if new period starts within existing period
                $subQ->where('valid_from', '<=', $this->valid_from)
                     ->where(function ($dateQ) {
                         $dateQ->whereNull('valid_to')
                               ->orWhere('valid_to', '>=', $this->valid_from);
                     });
            })->orWhere(function ($subQ) {
                // Check if existing period starts within new period
                $subQ->where('valid_from', '<=', $this->valid_to ?? Carbon::maxValue())
                     ->where(function ($dateQ) {
                         $dateQ->whereNull('valid_to')
                               ->orWhere('valid_to', '>=', $this->valid_from);
                     });
            });
        })->exists();
    }
}
