<?php

namespace App\Filament\Base\Resources\RoomTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RoomTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('capacity')
                    ->required()
                    ->numeric(),
                TextInput::make('description'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
