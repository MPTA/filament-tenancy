<?php

namespace App\Models\Tenants;

use App\Enums\TicketClassEnum;
use App\Enums\TransportModeEnum;
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
            'transport_mode' => TransportModeEnum::class,
        ];
    }

    protected static function booted(): void
    {
        // When breakdown ticket is updated, mark parent breakdown as incomplete
        static::updating(function ($ticket) {
            if ($ticket->isDirty()) {
                $ticket->breakdown()->update(['is_completed' => false]);
            }
        });

        // When breakdown ticket is created, mark parent breakdown as incomplete
        static::created(function ($ticket) {
            $ticket->breakdown()->update(['is_completed' => false]);
        });

        // When breakdown ticket is deleted, mark parent breakdown as incomplete
        static::deleted(function ($ticket) {
            $ticket->breakdown()->update(['is_completed' => false]);
        });
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
