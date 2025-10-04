<?php

namespace App\Models\Tenants;

use App\Enums\TicketClassEnum;
use App\Models\Base\City;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOfferLeaderTicket extends Model
{
    use HasUuids, BelongsToTenant;

    protected $table = 'quotation_offer_leader_tickets';

    protected $fillable = [
        'quotation_offer_id',
        'from_city_id',
        'to_city_id',
        'class',
        'price',
        'tenant_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'class' => TicketClassEnum::class,
    ];

    /**
     * Get the quotation offer that owns this ticket.
     */
    public function quotationOffer(): BelongsTo
    {
        return $this->belongsTo(QuotationOffer::class);
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
     * Scope to filter by offer.
     */
    public function scopeByOffer($query, $offerId)
    {
        return $query->where('quotation_offer_id', $offerId);
    }

    /**
     * Scope to filter by class.
     */
    public function scopeByClass($query, $class)
    {
        return $query->where('class', $class);
    }
}
