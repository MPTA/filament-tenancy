<?php

namespace App\Filament\Base\Resources\Accommodations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AccommodationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('content'),
                TextInput::make('star_rating')
                    ->numeric(),
                Textarea::make('address')
                    ->columnSpanFull(),
                TextInput::make('latitude')
                    ->numeric(),
                TextInput::make('longitude')
                    ->numeric(),
                Select::make('country_id')
                    ->relationship('country', 'name')
                    ->required(),
                Select::make('province_id')
                    ->relationship('province', 'name')
                    ->required(),
                Select::make('city_id')
                    ->relationship('city', 'name')
                    ->required(),
                Select::make('district_id')
                    ->relationship('district', 'name'),
                TextInput::make('external_id'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
