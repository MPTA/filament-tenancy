<?php

namespace App\Traits;

trait TranslatableEnum
{
    /**
     * Get the translated label for the enum value.
     */
    public function label(): string
    {
        $enumKey = $this->getEnumTranslationKey();
        return __("enums.{$enumKey}.{$this->value}");
    }

    /**
     * Get the translated description for the enum value.
     */
    public function description(): string
    {
        $enumKey = $this->getEnumTranslationKey();
        return __("enums.{$enumKey}.{$this->value}_description");
    }

    /**
     * Get the translation key for this enum.
     */
    private function getEnumTranslationKey(): string
    {
        return str(class_basename($this))
            ->beforeLast('Enum')
            ->snake()
            ->value();
    }

    /**
     * Get all options as key-value pairs (value => label).
     */
    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }

    /**
     * Get all options with descriptions as key-value pairs.
     */
    public static function getOptionsWithDescriptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [
                $case->value => [
                    'label' => $case->label(),
                    'description' => $case->description(),
                ]
            ])
            ->toArray();
    }
}

