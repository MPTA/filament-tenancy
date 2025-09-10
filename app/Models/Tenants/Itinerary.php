<?php

namespace App\Models\Tenants;

use App\Enums\TravelModeEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Itinerary extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'itineraryable_id',
        'itineraryable_type',
        'tenant_id',
        'travel_mode',
        'is_advanced',
        'is_complete',
        'is_vip',
        'creator_user_id',
    ];

    protected $casts = [
        'travel_mode' => TravelModeEnum::class,
        'is_advanced' => 'boolean',
        'is_complete' => 'boolean',
        'is_vip' => 'boolean',
    ];

    /**
     * Get the parent itineraryable model (polymorphic relationship).
     */
    public function itineraryable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the creator user.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_user_id');
    }

    /**
     * Get the tenant.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(\Stancl\Tenancy\Database\Models\Tenant::class);
    }

    /**
     * Get the itinerary days.
     */
    public function itineraryDays(): HasMany
    {
        return $this->hasMany(ItineraryDay::class);
    }
}
