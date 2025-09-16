<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationItinerary extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'quotation_id',
        'tenant_id',
    ];

    /**
     * Get the quotation associated with this itinerary.
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    /**
     * Get the quotation offer groups for this itinerary (one-to-many relationship).
     */
    public function quotationOfferGroups(): HasMany
    {
        return $this->hasMany(QuotationOfferGroup::class);
    }
}
