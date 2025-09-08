<?php

namespace App\Filament\Base\Resources\Provinces\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProvinceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('country_id')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('code'),
            ]);
    }
}
