<?php

namespace App\Models\Tenants;

use App\Enums\TicketClassEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class BreakdownTicket extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'breakdown_id',
        'tenant_id',
        'transport_mode',
        'from_city_id',
        'to_city_id',
        'class',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'class' => TicketClassEnum::class,
        ];
    }

    /**
     * Get the breakdown that owns this ticket pricing.
     */
    public function breakdown(): BelongsTo
    {
        return $this->belongsTo(Breakdown::class);
    }

    /**
     * Get the from city for this ticket.
     */
    public function fromCity(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Base\City::class, 'from_city_id');
    }

    /**
     * Get the to city for this ticket.
     */
    public function toCity(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Base\City::class, 'to_city_id');
    }
}
