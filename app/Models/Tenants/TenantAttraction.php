<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Illuminate\Support\Facades\Auth;

class TenantAttraction extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'attraction_id',
        'currency_id',
        'local_price',
        'foreigner_price',
        'additional_content',
        'creator_user_id',
    ];

    protected $casts = [
        'local_price' => 'decimal:2',
        'foreigner_price' => 'decimal:2',
        'additional_content' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->creator_user_id)) {
                $model->creator_user_id = Auth::id();
            }
        });
    }

    public function attraction(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Base\Attraction::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Base\Currency::class);
    }

    public function tenantSubAttractions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Tenants\TenantSubAttraction::class);
    }

    public function creatorUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'creator_user_id');
    }

    public function scopeForAttraction($query, $attractionId)
    {
        return $query->where('attraction_id', $attractionId);
    }

    public static function getCurrentPrice($attractionId)
    {
        return static::forAttraction($attractionId)->first();
    }

    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('creator_user_id', $userId);
    }

    public static function getPrices($attractionId)
    {
        return static::forAttraction($attractionId)->get();
    }
}
