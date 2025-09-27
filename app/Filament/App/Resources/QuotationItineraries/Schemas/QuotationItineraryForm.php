<?php

namespace App\Filament\App\Resources\QuotationItineraries\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class QuotationItineraryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('quotation_id')
                    ->relationship('quotation', 'id')
                    ->required(),
                TextInput::make('tenant_id')
                    ->required(),
                Toggle::make('is_foreigner_passengers')
                    ->label('Foreigner Passengers')
                    ->helperText('Check if this quotation is for foreign passengers')
                    ->default(false),
            ]);
    }
}
