<?php

namespace App\Filament\Base\Resources\Attractions;

use App\Filament\Base\Resources\Attractions\Pages\CreateAttraction;
use App\Filament\Base\Resources\Attractions\Pages\EditAttraction;
use App\Filament\Base\Resources\Attractions\Pages\ListAttractions;
use App\Filament\Base\Resources\Attractions\Pages\ViewAttraction;
use App\Filament\Base\Resources\Attractions\Schemas\AttractionForm;
use App\Filament\Base\Resources\Attractions\Schemas\AttractionInfolist;
use App\Filament\Base\Resources\Attractions\Tables\AttractionsTable;
use App\Models\Base\Attraction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class AttractionResource extends Resource
{
    use Translatable;
    protected static ?string $model = Attraction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AttractionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AttractionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttractionsTable::configure($table);
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
            'index' => ListAttractions::route('/'),
            'create' => CreateAttraction::route('/create'),
            'view' => ViewAttraction::route('/{record}'),
            'edit' => EditAttraction::route('/{record}/edit'),
        ];
    }
}
