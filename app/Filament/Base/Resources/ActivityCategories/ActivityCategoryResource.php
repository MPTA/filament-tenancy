<?php

namespace App\Filament\Base\Resources\ActivityCategories;

use App\Filament\Base\Resources\ActivityCategories\Pages\CreateActivityCategory;
use App\Filament\Base\Resources\ActivityCategories\Pages\EditActivityCategory;
use App\Filament\Base\Resources\ActivityCategories\Pages\ListActivityCategories;
use App\Filament\Base\Resources\ActivityCategories\Pages\ViewActivityCategory;
use App\Filament\Base\Resources\ActivityCategories\Schemas\ActivityCategoryForm;
use App\Filament\Base\Resources\ActivityCategories\Schemas\ActivityCategoryInfolist;
use App\Filament\Base\Resources\ActivityCategories\Tables\ActivityCategoriesTable;
use App\Models\Base\ActivityCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ActivityCategoryResource extends Resource
{
    protected static ?string $model = ActivityCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Activity Categories';

    protected static ?string $modelLabel = 'Activity Category';

    protected static ?string $pluralModelLabel = 'Activity Categories';

    protected static string | UnitEnum | null $navigationGroup = 'Categories';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->name . ' (' . $record->type->value . ')';
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Type' => $record->type->value ?? 'No type',
            'Slug' => $record->slug ?? 'No slug',
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug'];
    }

    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl('view', ['record' => $record]);
    }

    public static function form(Schema $schema): Schema
    {
        return ActivityCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ActivityCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ActivityCategoriesTable::configure($table);
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
            'index' => ListActivityCategories::route('/'),
            'create' => CreateActivityCategory::route('/create'),
            'view' => ViewActivityCategory::route('/{record}'),
            'edit' => EditActivityCategory::route('/{record}/edit'),
        ];
    }
}
