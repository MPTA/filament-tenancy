<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AccommodationPrice extends Model
{
    use HasUuids;

    /**
     * The table associated with the model.
     */
    protected $table = 'accommodation_prices';

    protected $with = ['accommodation.country.currency'];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id',
        'room_category_id',
        'accommodation_id',
        'price',
        'valid_from',
        'valid_to',
        'is_include_breakfast',
        'is_include_lunch',
        'is_include_dinner',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'valid_from' => 'date',
        'valid_to' => 'date',
        'is_include_breakfast' => 'boolean',
        'is_include_lunch' => 'boolean',
        'is_include_dinner' => 'boolean',
    ];


    /**
     * Get the currency_id attribute from the accommodation's country.
     */
    public function getCurrencyIdAttribute()
    {
        return $this->accommodation?->country?->currency_id;
    }

    /**
     * Get the accommodation that owns the price.
     */
    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }

    /**
     * Get the room category that owns the price.
     */
    public function roomCategory(): BelongsTo
    {
        return $this->belongsTo(RoomCategory::class);
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
}
