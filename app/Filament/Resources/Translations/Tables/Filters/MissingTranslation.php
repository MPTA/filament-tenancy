<?php

namespace App\Filament\Resources\Translations\Tables\Filters;

use Filament\Tables\Filters;
use Illuminate\Database\Eloquent\Builder;

class MissingTranslation extends Filter
{
    public static function make(): Filters\SelectFilter
    {
        $locales = config('filament-translations.locals', []);
        
        // Extract only labels for options
        $options = [];
        foreach ($locales as $code => $locale) {
            $options[$code] = $locale['label'] ?? $code;
        }
        
        return Filters\SelectFilter::make('missing_translation')
            ->label('Missing Translation')
            ->placeholder('Show all translations')
            ->options($options)
            ->query(function (Builder $query, array $data): Builder {
                $locale = $data['value'] ?? null;
                
                if (!$locale) {
                    return $query;
                }
                
                // Check if text->locale is null or empty
                return $query->where(function ($q) use ($locale) {
                    $q->whereRaw("(text->>?) IS NULL", [$locale])
                      ->orWhereRaw("(text->>?) = ''", [$locale]);
                });
            });
    }
}

