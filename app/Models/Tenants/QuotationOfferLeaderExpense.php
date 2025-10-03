<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOfferLeaderExpense extends Model
{
    use HasUuids, BelongsToTenant;

    protected $table = 'quotation_offer_leader_expenses';

    protected $fillable = [
        'quotation_offer_id',
        'description',
        'price',
        'tenant_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the quotation offer that owns this expense.
     */
    public function quotationOffer(): BelongsTo
    {
        return $this->belongsTo(QuotationOffer::class);
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2);
    }

    /**
     * Scope to filter by offer.
     */
    public function scopeByOffer($query, $offerId)
    {
        return $query->where('quotation_offer_id', $offerId);
    }

    /**
     * Scope to filter by description.
     */
    public function scopeByDescription($query, $description)
    {
        return $query->where('description', 'like', "%{$description}%");
    }
}
