<?php

namespace App\Filament\App\Resources\QuotationItineraries\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
            ]);
    }
}
