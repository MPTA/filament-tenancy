<?php

namespace App\Enums;

enum StarRatingEnum: int
{
    case ONE_STAR = 1;
    case TWO_STARS = 2;
    case THREE_STARS = 3;
    case FOUR_STARS = 4;
    case FIVE_STARS = 5;

    public function getLabel(): string
    {
        return (string) $this->value;
    }

    public function getFullLabel(): string
    {
        return match($this) {
            self::ONE_STAR => '1 Star',
            self::TWO_STARS => '2 Stars',
            self::THREE_STARS => '3 Stars',
            self::FOUR_STARS => '4 Stars',
            self::FIVE_STARS => '5 Stars',
        };
    }

    public function getDescription(): string
    {
        return match($this) {
            self::ONE_STAR => 'Basic accommodation with minimal amenities',
            self::TWO_STARS => 'Budget-friendly accommodation with basic facilities',
            self::THREE_STARS => 'Mid-range accommodation with good facilities and services',
            self::FOUR_STARS => 'High-quality accommodation with excellent facilities and services',
            self::FIVE_STARS => 'Luxury accommodation with exceptional facilities and services',
        };
    }

    public function getStars(): string
    {
        return str_repeat('★', $this->value);
    }

    public function getEmptyStars(): string
    {
        return str_repeat('☆', 5 - $this->value);
    }

    public function getFullDisplay(): string
    {
        return $this->getStars() . $this->getEmptyStars();
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getFullLabel()])
            ->toArray();
    }

    public static function getOptionsWithStars(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel() . ' ' . $case->getStars()])
            ->toArray();
    }
}
