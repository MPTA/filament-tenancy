<?php

namespace App\Enums;

enum VehicleUsageModeEnum: string
{
    case HOUR = 'hour';
    case HALF_DAY = 'half_day';
    case FULL_DAY = 'full_day';

    public function getLabel(): string
    {
        return match($this) {
            self::HOUR => 'Hour',
            self::HALF_DAY => 'Half Day',
            self::FULL_DAY => 'Full Day',
        };
    }

    public static function getOptions(): array
    {
        return [
            self::HOUR->value => self::HOUR->getLabel(),
            self::HALF_DAY->value => self::HALF_DAY->getLabel(),
            self::FULL_DAY->value => self::FULL_DAY->getLabel(),
        ];
    }
}
