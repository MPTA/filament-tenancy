<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class TenantSubAttractionPrice extends Model
{
    use HasUuids, BelongsToTenant;

    /**
     * The table associated with the model.
     */
    protected $table = 'tenant_sub_attraction_prices';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id',
        'tenant_id',
        'sub_attraction_id',
        'local_price',
        'foreigner_price',
        'additional_content',
        'creator_user_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'local_price' => 'decimal:2',
        'foreigner_price' => 'decimal:2',
        'additional_content' => 'array',
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
        });
    }

    /**
     * Get the sub attraction that owns the price.
     */
    public function subAttraction(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Base\SubAttraction::class);
    }


    /**
     * Get the user that created the price.
     */
    public function creatorUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'creator_user_id');
    }

    /**
     * Scope a query to only include prices for a specific sub attraction.
     */
    public function scopeForSubAttraction($query, $subAttractionId)
    {
        return $query->where('sub_attraction_id', $subAttractionId);
    }

    /**
     * Get the current price for sub attraction.
     */
    public static function getCurrentPrice($subAttractionId)
    {
        return static::forSubAttraction($subAttractionId)->first();
    }

    /**
     * Scope a query to only include prices created by a specific user.
     */
    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('creator_user_id', $userId);
    }

    /**
     * Get all prices for sub attraction.
     */
    public static function getPrices($subAttractionId)
    {
        return static::forSubAttraction($subAttractionId)->get();
    }
}
