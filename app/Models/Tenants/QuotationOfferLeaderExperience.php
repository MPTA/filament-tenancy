<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOfferLeaderExperience extends Model
{
    use HasUuids, BelongsToTenant;

    protected $table = 'quotation_offer_leader_experiences';

    protected $fillable = [
        'quotation_offer_id',
        'experience_id',
        'price',
        'tenant_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the quotation offer that owns this experience.
     */
    public function quotationOffer(): BelongsTo
    {
        return $this->belongsTo(QuotationOffer::class);
    }

    /**
     * Get the experience for this entry.
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
     * Scope to filter by offer.
     */
    public function scopeByOffer($query, $offerId)
    {
        return $query->where('quotation_offer_id', $offerId);
    }

    /**
     * Scope to filter by experience.
     */
    public function scopeByExperience($query, $experienceId)
    {
        return $query->where('experience_id', $experienceId);
    }
}
