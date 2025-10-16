<?php

namespace App\Enums;

use App\Traits\TranslatableEnum;

enum StarRatingEnum: int
{
    use TranslatableEnum;

    case ONE_STAR = 1;
    case TWO_STARS = 2;
    case THREE_STARS = 3;
    case FOUR_STARS = 4;
    case FIVE_STARS = 5;

    /**
     * Get the translation key for this enum.
     * Override to handle integer-backed enum.
     */
    private function getEnumTranslationKey(): string
    {
        return 'star_rating';
    }

    public function getLabel(): string
    {
        return (string) $this->value;
    }

    public function getFullLabel(): string
    {
        return $this->label();
    }

    public function getDescription(): string
    {
        return $this->description();
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

    public static function getOptionsWithStars(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel() . ' ' . $case->getStars()])
            ->toArray();
    }
}
