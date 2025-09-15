<?php

namespace App\Data;

use App\Enums\TicketClassEnum;
use App\Enums\TransportModeEnum;
use App\Models\Base\City;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class BreakdownTicketData extends Data
{
    public function __construct(
        public TransportModeEnum $transport_mode,
        public City $from_city,
        public City $to_city,
        public TicketClassEnum $class,
        #[Min(0)]
        public float $price,
    ) {}

    /**
     * Get formatted price
     */
    public function getFormattedPrice(): string
    {
        return number_format($this->price, 2);
    }

    /**
     * Get transport mode label
     */
    public function getTransportModeLabel(): string
    {
        return $this->transport_mode->label();
    }

    /**
     * Get ticket class label
     */
    public function getTicketClassLabel(): string
    {
        return $this->class->label();
    }

    /**
     * Get route description
     */
    public function getRouteDescription(): string
    {
        return $this->from_city->name . ' → ' . $this->to_city->name;
    }
}
