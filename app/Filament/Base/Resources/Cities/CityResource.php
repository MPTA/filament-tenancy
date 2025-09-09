<?php

namespace App\Filament\Base\Resources\Cities;

use App\Filament\Base\Resources\Cities\Pages\CreateCity;
use App\Filament\Base\Resources\Cities\Pages\EditCity;
use App\Filament\Base\Resources\Cities\Pages\ListCities;
use App\Filament\Base\Resources\Cities\Pages\ViewCity;
use App\Filament\Base\Resources\Cities\RelationManagers\DistrictsRelationManager;
use App\Filament\Base\Resources\Cities\Schemas\CityForm;
use App\Filament\Base\Resources\Cities\Schemas\CityInfolist;
use App\Filament\Base\Resources\Cities\Tables\CitiesTable;
use App\Models\Base\City;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CityForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CityInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CitiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DistrictsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCities::route('/'),
            'create' => CreateCity::route('/create'),
            'view' => ViewCity::route('/{record}'),
            'edit' => EditCity::route('/{record}/edit'),
        ];
    }
}
