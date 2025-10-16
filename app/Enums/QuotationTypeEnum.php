<?php

namespace App\Enums;

use App\Traits\TranslatableEnum;

enum QuotationTypeEnum: string
{
    use TranslatableEnum;

    case GENERAL = 'general';
    case ITINERARY = 'itinerary';

    /**
     * Backward compatibility alias for getLabel()
     */
    public function getLabel(): string
    {
        return $this->label();
    }
}
