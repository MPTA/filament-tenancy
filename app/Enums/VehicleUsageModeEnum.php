<?php

namespace App\Enums;

use App\Traits\TranslatableEnum;

enum VehicleUsageModeEnum: string
{
    use TranslatableEnum;

    case HOUR = 'hour';
    case HALF_DAY = 'half_day';
    case FULL_DAY = 'full_day';

    /**
     * Backward compatibility alias for getLabel()
     */
    public function getLabel(): string
    {
        return $this->label();
    }
}
