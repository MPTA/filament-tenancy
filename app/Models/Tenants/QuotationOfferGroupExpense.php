<?php

namespace App\Models\Tenants;

use App\Enums\ChargeModeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOfferGroupExpense extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'quotation_offer_group_id',
        'description',
        'price',
        'charge_mode',
        'tenant_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'charge_mode' => ChargeModeEnum::class,
    ];

    /**
     * Get the quotation offer group for this expense.
     */
    public function quotationOfferGroup(): BelongsTo
    {
        return $this->belongsTo(QuotationOfferGroup::class);
    }

    /**
     * Scope a query to filter by quotation offer group.
     */
    public function scopeByQuotationOfferGroup($query, $quotationOfferGroupId)
    {
        return $query->where('quotation_offer_group_id', $quotationOfferGroupId);
    }

    /**
     * Scope a query to filter by charge mode.
     */
    public function scopeByChargeMode($query, ChargeModeEnum|string $chargeMode)
    {
        $chargeModeValue = $chargeMode instanceof ChargeModeEnum ? $chargeMode->value : $chargeMode;
        return $query->where('charge_mode', $chargeModeValue);
    }

    /**
     * Scope a query to filter by per person charge mode.
     */
    public function scopePerPerson($query)
    {
        return $query->where('charge_mode', ChargeModeEnum::PER_PERSON->value);
    }

    /**
     * Scope a query to filter by per group charge mode.
     */
    public function scopePerGroup($query)
    {
        return $query->where('charge_mode', ChargeModeEnum::PER_GROUP->value);
    }

    /**
     * Scope a query to filter by price range.
     */
    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope a query to search by description.
     */
    public function scopeSearchDescription($query, $search)
    {
        return $query->where('description', 'ILIKE', "%{$search}%");
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->price, 2);
    }

    /**
     * Get the charge mode label.
     */
    public function getChargeModeLabelAttribute(): string
    {
        return $this->charge_mode?->label() ?? '';
    }

    /**
     * Get the charge mode description.
     */
    public function getChargeModeDescriptionAttribute(): string
    {
        return $this->charge_mode?->description() ?? '';
    }

    /**
     * Check if this expense is charged per person.
     */
    public function getIsPerPersonAttribute(): bool
    {
        return $this->charge_mode === ChargeModeEnum::PER_PERSON;
    }

    /**
     * Check if this expense is charged per group.
     */
    public function getIsPerGroupAttribute(): bool
    {
        return $this->charge_mode === ChargeModeEnum::PER_GROUP;
    }

    /**
     * Calculate total cost based on charge mode and quantity.
     */
    public function calculateTotalCost(int $personCount = 1): float
    {
        if ($this->charge_mode === ChargeModeEnum::PER_PERSON) {
            return (float) $this->price * $personCount;
        }
        
        return (float) $this->price;
    }

    /**
     * Get formatted total cost based on charge mode and quantity.
     */
    public function getFormattedTotalCost(int $personCount = 1): string
    {
        return number_format($this->calculateTotalCost($personCount), 2);
    }
}
