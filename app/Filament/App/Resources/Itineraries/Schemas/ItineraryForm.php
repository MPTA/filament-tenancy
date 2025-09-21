<?php

namespace App\Filament\App\Resources\Itineraries\Schemas;

use App\Enums\TravelModeEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ItineraryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('travel_mode')
                    ->options(TravelModeEnum::class)
                    ->required(),
                Toggle::make('is_advanced')
                    ->required(),
                Toggle::make('is_complete')
                    ->required(),
                Toggle::make('is_vip')
                    ->required(),
                TextInput::make('creator_user_id')
                    ->required(),
                TextInput::make('itineraryable_type')
                    ->required(),
                TextInput::make('itineraryable_id')
                    ->required(),
            ]);
    }
}
