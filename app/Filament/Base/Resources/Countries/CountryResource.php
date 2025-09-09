<?php

namespace App\Filament\Base\Resources\Countries;

use App\Filament\Base\Resources\Countries\Pages\CreateCountry;
use App\Filament\Base\Resources\Countries\Pages\EditCountry;
use App\Filament\Base\Resources\Countries\Pages\ListCountries;
use App\Filament\Base\Resources\Countries\Pages\ViewCountry;
use App\Filament\Base\Resources\Countries\RelationManagers\ProvincesRelationManager;
use App\Filament\Base\Resources\Countries\Schemas\CountryForm;
use App\Filament\Base\Resources\Countries\Schemas\CountryInfolist;
use App\Filament\Base\Resources\Countries\Tables\CountriesTable;
use App\Models\Base\Country;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class CountryResource extends Resource
{
    use Translatable;
    protected static ?string $model = Country::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CountryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CountryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CountriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ProvincesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCountries::route('/'),
            'create' => CreateCountry::route('/create'),
            'view' => ViewCountry::route('/{record}'),
            'edit' => EditCountry::route('/{record}/edit'),
        ];
    }
}
