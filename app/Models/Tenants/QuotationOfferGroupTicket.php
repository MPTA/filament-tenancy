<?php

namespace App\Models\Tenants;

use App\Enums\TicketClassEnum;
use App\Models\Base\City;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOfferGroupTicket extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'quotation_offer_group_id',
        'from_city_id',
        'to_city_id',
        'class',
        'price',
        'tenant_id',
    ];

    protected $casts = [
        'class' => TicketClassEnum::class,
        'price' => 'decimal:2',
    ];

    /**
     * Get the quotation offer group for this ticket.
     */
    public function quotationOfferGroup(): BelongsTo
    {
        return $this->belongsTo(QuotationOfferGroup::class);
    }

    /**
     * Get the from city for this ticket.
     */
    public function fromCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'from_city_id');
    }

    /**
     * Get the to city for this ticket.
     */
    public function toCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'to_city_id');
    }

    /**
     * Scope a query to filter by quotation offer group.
     */
    public function scopeByQuotationOfferGroup($query, $quotationOfferGroupId)
    {
        return $query->where('quotation_offer_group_id', $quotationOfferGroupId);
    }

    /**
     * Scope a query to filter by from city.
     */
    public function scopeByFromCity($query, $fromCityId)
    {
        return $query->where('from_city_id', $fromCityId);
    }

    /**
     * Scope a query to filter by to city.
     */
    public function scopeByToCity($query, $toCityId)
    {
        return $query->where('to_city_id', $toCityId);
    }

    /**
     * Scope a query to filter by class.
     */
    public function scopeByClass($query, TicketClassEnum|string $class)
    {
        $classValue = $class instanceof TicketClassEnum ? $class->value : $class;
        return $query->where('class', $classValue);
    }

    /**
     * Scope a query to filter by price range.
     */
    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope a query to filter by route (from and to cities).
     */
    public function scopeByRoute($query, $fromCityId, $toCityId)
    {
        return $query->where('from_city_id', $fromCityId)
                     ->where('to_city_id', $toCityId);
    }

    /**
     * Get the from city name.
     */
    public function getFromCityNameAttribute(): ?string
    {
        return $this->fromCity?->name;
    }

    /**
     * Get the to city name.
     */
    public function getToCityNameAttribute(): ?string
    {
        return $this->toCity?->name;
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->price, 2);
    }

    /**
     * Get the route description.
     */
    public function getRouteDescriptionAttribute(): string
    {
        return ($this->from_city_name ?? 'N/A') . ' → ' . ($this->to_city_name ?? 'N/A');
    }

    /**
     * Get the ticket description.
     */
    public function getTicketDescriptionAttribute(): string
    {
        $classLabel = $this->class?->label() ?? $this->class;
        return $this->route_description . ' (' . $classLabel . ')';
    }

    /**
     * Get the class label.
     */
    public function getClassLabelAttribute(): string
    {
        return $this->class?->label() ?? '';
    }

    /**
     * Get the class description.
     */
    public function getClassDescriptionAttribute(): string
    {
        return $this->class?->description() ?? '';
    }
}
