<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOfferGroupCompanionExpense extends Model
{
    use HasUuids, BelongsToTenant;

    protected $table = 'quotation_offer_group_companion_expenses';

    protected $fillable = [
        'quotation_offer_group_companion_id',
        'description',
        'price',
        'tenant_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the quotation offer group companion that owns this expense.
     */
    public function quotationOfferGroupCompanion(): BelongsTo
    {
        return $this->belongsTo(QuotationOfferGroupCompanion::class);
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
     * Scope to filter by description.
     */
    public function scopeByDescription($query, $description)
    {
        return $query->where('description', 'like', "%{$description}%");
    }
}
