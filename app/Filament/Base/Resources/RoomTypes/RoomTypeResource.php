<?php

namespace App\Filament\Base\Resources\RoomTypes;

use App\Filament\Base\Resources\RoomTypes\Pages\CreateRoomType;
use App\Filament\Base\Resources\RoomTypes\Pages\EditRoomType;
use App\Filament\Base\Resources\RoomTypes\Pages\ListRoomTypes;
use App\Filament\Base\Resources\RoomTypes\Pages\ViewRoomType;
use App\Filament\Base\Resources\RoomTypes\Schemas\RoomTypeForm;
use App\Filament\Base\Resources\RoomTypes\Schemas\RoomTypeInfolist;
use App\Filament\Base\Resources\RoomTypes\Tables\RoomTypesTable;
use App\Models\Base\RoomType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class RoomTypeResource extends Resource
{
    use Translatable;
    protected static ?string $model = RoomType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RoomTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RoomTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RoomTypesTable::configure($table);
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
            'index' => ListRoomTypes::route('/'),
            'create' => CreateRoomType::route('/create'),
            'view' => ViewRoomType::route('/{record}'),
            'edit' => EditRoomType::route('/{record}/edit'),
        ];
    }
}
