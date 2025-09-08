<?php

namespace App\Filament\Base\Resources\Provinces;

use App\Filament\Base\Resources\Provinces\Pages\CreateProvince;
use App\Filament\Base\Resources\Provinces\Pages\EditProvince;
use App\Filament\Base\Resources\Provinces\Pages\ListProvinces;
use App\Filament\Base\Resources\Provinces\Pages\ViewProvince;
use App\Filament\Base\Resources\Provinces\Schemas\ProvinceForm;
use App\Filament\Base\Resources\Provinces\Schemas\ProvinceInfolist;
use App\Filament\Base\Resources\Provinces\Tables\ProvincesTable;
use App\Models\Base\Province;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProvinceResource extends Resource
{
    protected static ?string $model = Province::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ProvinceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProvinceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProvincesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProvinces::route('/'),
            'create' => CreateProvince::route('/create'),
            'view' => ViewProvince::route('/{record}'),
            'edit' => EditProvince::route('/{record}/edit'),
        ];
    }
}
