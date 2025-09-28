<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Illuminate\Support\Facades\Auth;

class TenantSubAttraction extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_attraction_id',
        'sub_attraction_id',
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

    public function tenantAttraction(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Tenants\TenantAttraction::class);
    }

    public function subAttraction(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Base\SubAttraction::class);
    }

    public function creatorUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'creator_user_id');
    }

    public function scopeForSubAttraction($query, $subAttractionId)
    {
        return $query->where('sub_attraction_id', $subAttractionId);
    }

    public static function getCurrentPrice($subAttractionId)
    {
        return static::forSubAttraction($subAttractionId)->first();
    }

    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('creator_user_id', $userId);
    }

    public static function getPrices($subAttractionId)
    {
        return static::forSubAttraction($subAttractionId)->get();
    }
}
