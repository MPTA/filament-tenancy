<?php

namespace App\Filament\Shared\Concerns;

trait TranslatableUiLocale
{
    protected function getAllowedLocales(): array
    {
        // Get configured locales from Resource
        return static::getResource()::getTranslatableLocales();
    }

    protected function resolveUiLocale(): string
    {
        $cookie = request()->cookie('filament_language_switch_locale');
        if (is_string($cookie) && $cookie !== '') {
            try {
                $decrypted = decrypt($cookie);
                if (is_string($decrypted) && $decrypted !== '') {
                    return $decrypted;
                }
            } catch (\Throwable $e) {
                return $cookie;
            }
        }

        return app()->getLocale();
    }

    /**
     * Normalize locale to match available translations.
     * Handles locale variants (e.g., zh, zh_CN, zh-CN).
     */
    protected function normalizeLocale(string $locale): string
    {
        $allowed = $this->getAllowedLocales();
        
        // If locale is already in allowed list, return as-is
        if (in_array($locale, $allowed, true)) {
            return $locale;
        }
        
        // Try to find a variant match (e.g., zh → zh_CN, en-US → en)
        $baseLocale = explode('_', explode('-', $locale)[0])[0];
        
        foreach ($allowed as $allowedLocale) {
            $allowedBase = explode('_', explode('-', $allowedLocale)[0])[0];
            if ($baseLocale === $allowedBase) {
                return $allowedLocale;
            }
        }
        
        // No match found, return original
        return $locale;
    }

    protected function mapToAvailableLocale(string $uiLocale, array $availableLocales): ?string
    {
        return in_array($uiLocale, $availableLocales, true) ? $uiLocale : null;
    }

    protected function getDefaultTranslatableLocale(): string
    {
        $ui = $this->normalizeLocale($this->resolveUiLocale());

        $resource = static::getResource();
        $attrs = $resource::getTranslatableAttributes();

        if (empty($attrs)) {
            return $ui;
        }

        $record = $this->getRecord();
        $available = array_keys($record->getTranslations($attrs[0]));

        if (empty($available)) {
            return $ui;
        }

        // Try UI locale first; if not found, fallback to first available to ensure form fields are filled
        return $this->mapToAvailableLocale($ui, $available) ?? $available[0];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $ui = $this->normalizeLocale($this->resolveUiLocale());

        $resource = static::getResource();
        $attrs = $resource::getTranslatableAttributes();

        if (! empty($attrs)) {
            $record = $this->getRecord();
            $available = array_keys($record->getTranslations($attrs[0]));
            $targetLocale = $this->mapToAvailableLocale($ui, $available) ?? $ui;
            
            $currentActiveLocale = $this->activeLocale ?? null;
            
            // If current activeLocale is different from target (e.g., form was filled with 'en' but we want to save 'zh_CN')
            // The user typed in the form which was displayed in currentActiveLocale (en)
            // But we want to save it as targetLocale (zh_CN)
            // So we need to:
            // 1. Restore the original value for currentActiveLocale from the record
            // 2. Keep user's input as the value for targetLocale
            if ($currentActiveLocale && $currentActiveLocale !== $targetLocale) {
                // The current form data ($data) is what user typed, and should go to targetLocale
                // But we need to preserve the original value for currentActiveLocale
                
                // Read original translations for currentActiveLocale from the record
                $originalCurrentLocaleData = [];
                foreach ($attrs as $attr) {
                    $originalCurrentLocaleData[$attr] = $record->getTranslation($attr, $currentActiveLocale);
                }
                
                // Store original currentActiveLocale data to otherLocaleData
                $this->otherLocaleData[$currentActiveLocale] = $originalCurrentLocaleData;
                
                // Remove targetLocale from otherLocaleData if exists (we want fresh user input)
                unset($this->otherLocaleData[$targetLocale]);
            }
            
            $this->activeLocale = $targetLocale;
        }

        // Filter otherLocaleData to only allowed locales to prevent unwanted keys like 'zh'
        if (property_exists($this, 'otherLocaleData') && is_array($this->otherLocaleData)) {
            $allowed = $this->getAllowedLocales();
            foreach (array_keys($this->otherLocaleData) as $locale) {
                if (! in_array($locale, $allowed, true)) {
                    unset($this->otherLocaleData[$locale]);
                }
            }
        }

        return parent::mutateFormDataBeforeSave($data);
    }

    /**
     * After saving, prune empty variant keys (e.g., an empty 'zh' alongside a real 'zh_CN').
     */
    protected function afterSave(): void
    {
        $resource = static::getResource();
        $attrs = $resource::getTranslatableAttributes();

        if (empty($attrs)) {
            return;
        }

        $record = $this->getRecord();
        $updated = false;

        foreach ($attrs as $attr) {
            $t = $record->getTranslations($attr);
            $allowed = $this->getAllowedLocales();

            // Keep only allowed locales and drop empty values
            $new = [];
            foreach ($t as $locale => $value) {
                if (in_array($locale, $allowed, true) && $value !== null && $value !== '') {
                    $new[$locale] = $value;
                }
            }

            if ($new !== $t) {
                $updated = true;
                $t = $new;
            }

            if ($updated) {
                // Re-assign cleaned translations for this attribute
                $record->setTranslations($attr, $t);
            }
        }

        if ($updated) {
            $record->save();
        }

        // Do not call parent::afterSave() to avoid BadMethodCall when parent method is not defined
    }
}


