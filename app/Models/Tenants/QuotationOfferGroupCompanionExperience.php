<?php

namespace App\Models\Tenants;

use App\Models\Tenants\Experience;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOfferGroupCompanionExperience extends Model
{
    use HasUuids, BelongsToTenant;

    protected $table = 'quotation_offer_group_companion_experiences';

    protected $fillable = [
        'quotation_offer_group_companion_id',
        'experience_id',
        'price',
        'tenant_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the quotation offer group companion that owns this experience.
     */
    public function quotationOfferGroupCompanion(): BelongsTo
    {
        return $this->belongsTo(QuotationOfferGroupCompanion::class);
    }

    /**
     * Get the experience for this cost.
     */
    public function experience(): BelongsTo
    {
        return $this->belongsTo(Experience::class);
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->price, 2);
    }

    /**
     * Scope to filter by companion.
     */
    public function scopeByCompanion($query, $companionId)
    {
        return $query->where('quotation_offer_group_companion_id', $companionId);
    }

    /**
     * Scope to filter by experience.
     */
    public function scopeByExperience($query, $experienceId)
    {
        return $query->where('experience_id', $experienceId);
    }
}
