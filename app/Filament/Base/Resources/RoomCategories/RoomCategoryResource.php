<?php

namespace App\Filament\Base\Resources\RoomCategories;

use App\Filament\Base\Resources\RoomCategories\Pages\CreateRoomCategory;
use App\Filament\Base\Resources\RoomCategories\Pages\EditRoomCategory;
use App\Filament\Base\Resources\RoomCategories\Pages\ListRoomCategories;
use App\Filament\Base\Resources\RoomCategories\Pages\ViewRoomCategory;
use App\Filament\Base\Resources\RoomCategories\Schemas\RoomCategoryForm;
use App\Filament\Base\Resources\RoomCategories\Schemas\RoomCategoryInfolist;
use App\Filament\Base\Resources\RoomCategories\Tables\RoomCategoriesTable;
use App\Models\Base\RoomCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class RoomCategoryResource extends Resource
{
    use Translatable;
    protected static ?string $model = RoomCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RoomCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RoomCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RoomCategoriesTable::configure($table);
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
            'index' => ListRoomCategories::route('/'),
            'create' => CreateRoomCategory::route('/create'),
            'view' => ViewRoomCategory::route('/{record}'),
            'edit' => EditRoomCategory::route('/{record}/edit'),
        ];
    }
}
