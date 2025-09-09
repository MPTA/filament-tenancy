<?php

namespace App\Filament\Base\Resources\MealCategories;

use App\Filament\Base\Resources\MealCategories\Pages\CreateMealCategory;
use App\Filament\Base\Resources\MealCategories\Pages\EditMealCategory;
use App\Filament\Base\Resources\MealCategories\Pages\ListMealCategories;
use App\Filament\Base\Resources\MealCategories\Pages\ViewMealCategory;
use App\Filament\Base\Resources\MealCategories\Schemas\MealCategoryForm;
use App\Filament\Base\Resources\MealCategories\Schemas\MealCategoryInfolist;
use App\Filament\Base\Resources\MealCategories\Tables\MealCategoriesTable;
use App\Models\Base\MealCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MealCategoryResource extends Resource
{
    protected static ?string $model = MealCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCake;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return MealCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MealCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MealCategoriesTable::configure($table);
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
            'index' => ListMealCategories::route('/'),
            'create' => CreateMealCategory::route('/create'),
            'view' => ViewMealCategory::route('/{record}'),
            'edit' => EditMealCategory::route('/{record}/edit'),
        ];
    }
}
