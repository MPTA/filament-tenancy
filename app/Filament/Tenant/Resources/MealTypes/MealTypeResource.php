<?php

namespace App\Filament\Tenant\Resources\MealTypes;

use App\Filament\Tenant\Resources\MealTypes\Pages\CreateMealType;
use App\Filament\Tenant\Resources\MealTypes\Pages\EditMealType;
use App\Filament\Tenant\Resources\MealTypes\Pages\ListMealTypes;
use App\Filament\Tenant\Resources\MealTypes\Pages\ViewMealType;
use App\Filament\Tenant\Resources\MealTypes\Schemas\MealTypeForm;
use App\Filament\Tenant\Resources\MealTypes\Schemas\MealTypeInfolist;
use App\Filament\Tenant\Resources\MealTypes\Tables\MealTypesTable;
use App\Models\Tenants\MealType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MealTypeResource extends Resource
{
    protected static ?string $model = MealType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCake;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Meal Types';

    protected static ?string $modelLabel = 'Meal Type';

    protected static ?string $pluralModelLabel = 'Meal Types';

    protected static string | UnitEnum | null $navigationGroup = 'Meals';

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
        return $record->name . ' (' . ($record->mealCategory->name ?? 'No Category') . ')';
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Category' => $record->mealCategory->name ?? 'No category',
            'Price' => $record->price ? '$' . number_format($record->price, 2) : 'Not specified',
            'Description' => $record->description ? substr(strip_tags($record->description), 0, 50) . '...' : 'No description',
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description'];
    }

    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl('view', ['record' => $record]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['mealCategory']);
    }

    public static function form(Schema $schema): Schema
    {
        return MealTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MealTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MealTypesTable::configure($table);
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
            'index' => ListMealTypes::route('/'),
            'create' => CreateMealType::route('/create'),
            'view' => ViewMealType::route('/{record}'),
            'edit' => EditMealType::route('/{record}/edit'),
        ];
    }
}
