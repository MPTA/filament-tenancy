<?php

namespace App\Filament\Base\Resources\Accommodations;

use App\Filament\Base\Resources\Accommodations\Pages\CreateAccommodation;
use App\Filament\Base\Resources\Accommodations\Pages\EditAccommodation;
use App\Filament\Base\Resources\Accommodations\Pages\ListAccommodations;
use App\Filament\Base\Resources\Accommodations\Pages\ViewAccommodation;
use App\Filament\Base\Resources\Accommodations\Schemas\AccommodationForm;
use App\Filament\Base\Resources\Accommodations\Schemas\AccommodationInfolist;
use App\Filament\Base\Resources\Accommodations\Tables\AccommodationsTable;
use App\Models\Base\Accommodation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class AccommodationResource extends Resource
{
    use Translatable;
    protected static ?string $model = Accommodation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AccommodationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AccommodationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccommodationsTable::configure($table);
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
            'index' => ListAccommodations::route('/'),
            'create' => CreateAccommodation::route('/create'),
            'view' => ViewAccommodation::route('/{record}'),
            'edit' => EditAccommodation::route('/{record}/edit'),
        ];
    }
}
