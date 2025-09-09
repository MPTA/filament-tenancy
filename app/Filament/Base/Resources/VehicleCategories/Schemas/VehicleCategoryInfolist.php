<?php

namespace App\Filament\Base\Resources\VehicleCategories\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class VehicleCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->translateLabel()
                    ->label('Vehicle Category Name'),
                TextEntry::make('slug')
                    ->label('URL Slug')
                    ->copyable(),
                TextEntry::make('description')
                    ->translateLabel()
                    ->label('Description')
                    ->columnSpanFull(),
                IconEntry::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextEntry::make('created_at')
                    ->label('Created At')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->label('Updated At')
                    ->dateTime(),
            ]);
    }
}
