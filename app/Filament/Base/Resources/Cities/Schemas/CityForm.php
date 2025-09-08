<?php

namespace App\Filament\Base\Resources\Cities\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('province_id'),
                TextInput::make('name')
                    ->required(),
                TextInput::make('code'),
            ]);
    }
}
