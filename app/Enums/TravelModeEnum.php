<?php

namespace App\Enums;

use App\Traits\TranslatableEnum;

enum TravelModeEnum: string
{
    use TranslatableEnum;

    case AIR = 'air';
    case SELF_DRIVING = 'self_driving';
    case AIR_SELF_DRIVING = 'air_self_driving';
    case SELF_DRIVING_AIR = 'self_driving_air';

    /**
     * Backward compatibility alias for getLabel()
     */
    public function getLabel(): string
    {
        return $this->label();
    }

    /**
     * Backward compatibility alias for getDescription()
     */
    public function getDescription(): string
    {
        return $this->description();
    }
}
