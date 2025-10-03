<?php

namespace App\Models\Tenants;

use App\Models\Base\City;
use App\Enums\TicketClassEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOfferGroupCompanionTicket extends Model
{
    use HasUuids, BelongsToTenant;

    protected $table = 'quotation_offer_group_companion_tickets';

    protected $fillable = [
        'quotation_offer_group_companion_id',
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
     * Get the quotation offer group companion that owns this ticket.
     */
    public function quotationOfferGroupCompanion(): BelongsTo
    {
        return $this->belongsTo(QuotationOfferGroupCompanion::class);
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
        return $this->fromCity?->name . ' → ' . $this->toCity?->name;
    }

    /**
     * Scope to filter by companion.
     */
    public function scopeByCompanion($query, $companionId)
    {
        return $query->where('quotation_offer_group_companion_id', $companionId);
    }

    /**
     * Scope to filter by from city.
     */
    public function scopeByFromCity($query, $cityId)
    {
        return $query->where('from_city_id', $cityId);
    }

    /**
     * Scope to filter by to city.
     */
    public function scopeByToCity($query, $cityId)
    {
        return $query->where('to_city_id', $cityId);
    }

    /**
     * Scope to filter by ticket class.
     */
    public function scopeByClass($query, $class)
    {
        return $query->where('class', $class);
    }
}
