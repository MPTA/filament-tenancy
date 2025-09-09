<?php

namespace App\Filament\Base\Resources\CompanionCategories;

use App\Filament\Base\Resources\CompanionCategories\Pages\CreateCompanionCategory;
use App\Filament\Base\Resources\CompanionCategories\Pages\EditCompanionCategory;
use App\Filament\Base\Resources\CompanionCategories\Pages\ListCompanionCategories;
use App\Filament\Base\Resources\CompanionCategories\Pages\ViewCompanionCategory;
use App\Filament\Base\Resources\CompanionCategories\Schemas\CompanionCategoryForm;
use App\Filament\Base\Resources\CompanionCategories\Schemas\CompanionCategoryInfolist;
use App\Filament\Base\Resources\CompanionCategories\Tables\CompanionCategoriesTable;
use App\Models\Base\CompanionCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class CompanionCategoryResource extends Resource
{
    use Translatable;
    protected static ?string $model = CompanionCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CompanionCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CompanionCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompanionCategoriesTable::configure($table);
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
            'index' => ListCompanionCategories::route('/'),
            'create' => CreateCompanionCategory::route('/create'),
            'view' => ViewCompanionCategory::route('/{record}'),
            'edit' => EditCompanionCategory::route('/{record}/edit'),
        ];
    }
}
