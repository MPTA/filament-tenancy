<?php

namespace App\Data;

use App\Enums\ChargeModeEnum;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class BreakdownExpensesData extends Data
{
    public function __construct(
        #[Required]
        public string $description,
        #[Min(0)]
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
}
