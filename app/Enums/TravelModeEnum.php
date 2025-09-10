<?php

namespace App\Enums;

enum TravelModeEnum: string
{
    case AIR = 'air';
    case SELF_DRIVING = 'self_driving';
    case AIR_SELF_DRIVING = 'air_self_driving';
    case SELF_DRIVING_AIR = 'self_driving_air';

    public function getLabel(): string
    {
        return match($this) {
            self::AIR => 'Air Travel',
            self::SELF_DRIVING => 'Self Driving',
            self::AIR_SELF_DRIVING => 'Air + Self Driving',
            self::SELF_DRIVING_AIR => 'Self Driving + Air',
        };
    }

    public function getDescription(): string
    {
        return match($this) {
            self::AIR => 'Travel by airplane only',
            self::SELF_DRIVING => 'Travel by car/vehicle only',
            self::AIR_SELF_DRIVING => 'Air travel followed by self driving',
            self::SELF_DRIVING_AIR => 'Self driving followed by air travel',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
