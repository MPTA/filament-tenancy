<?php

namespace App\Data;

use App\Enums\ChargeModeEnum;
use App\Models\Tenants\Experience;
use Spatie\LaravelData\Data;

class BreakdownExperienceData extends Data
{
    public function __construct(
        public Experience $experience,
        public float $price,
        public ChargeModeEnum $charge_mode,
    ) {}

    /**
     * Get formatted price
     */
    public function getFormattedPrice(): string
    {
        return number_format($this->price, 2);
    }

    /**
     * Get charge mode label
     */
    public function getChargeModeLabel(): string
    {
        return $this->charge_mode->label();
    }

    /**
     * Get charge mode description
     */
    public function getChargeModeDescription(): string
    {
        return $this->charge_mode->description();
    }

    /**
     * Get experience name
     */
    public function getExperienceName(): string
    {
        return $this->experience->getTranslation('name', app()->getLocale()) ?? $this->experience->name['en'] ?? '';
    }

    /**
     * Get experience description
     */
    public function getExperienceDescription(): string
    {
        return $this->experience->getTranslation('description', app()->getLocale()) ?? $this->experience->description['en'] ?? '';
    }
}
