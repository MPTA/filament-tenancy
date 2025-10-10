<?php

namespace App\Filament\Tenant\Resources\CompanionTypes;

use App\Filament\Tenant\Resources\CompanionTypes\Pages\CreateCompanionType;
use App\Filament\Tenant\Resources\CompanionTypes\Pages\EditCompanionType;
use App\Filament\Tenant\Resources\CompanionTypes\Pages\ListCompanionTypes;
use App\Filament\Tenant\Resources\CompanionTypes\Pages\ViewCompanionType;
use App\Filament\Tenant\Resources\CompanionTypes\Schemas\CompanionTypeForm;
use App\Filament\Tenant\Resources\CompanionTypes\Schemas\CompanionTypeInfolist;
use App\Filament\Tenant\Resources\CompanionTypes\Tables\CompanionTypesTable;
use App\Models\Tenants\CompanionType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use UnitEnum;

class CompanionTypeResource extends Resource
{
    use Translatable;
    protected static ?string $model = CompanionType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Companion Types';

    protected static ?string $modelLabel = 'Companion Type';

    protected static ?string $pluralModelLabel = 'Companion Types';

    protected static string | UnitEnum | null $navigationGroup = 'Data Types';

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
        return $record->name . ' (' . ($record->companionCategory->name ?? 'No Category') . ')';
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Category' => $record->companionCategory->name ?? 'No category',
            'Native Language' => $record->nativeLanguage->name ?? 'Not specified',
            'Speaking Language' => $record->speakingLanguage->name ?? 'Not specified',
            'Per Day Price' => $record->per_day_price ? '$' . number_format($record->per_day_price, 2) : 'Not set',
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

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['companionCategory', 'nativeLanguage', 'speakingLanguage', 'currency']);
    }

    public static function form(Schema $schema): Schema
    {
        return CompanionTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CompanionTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompanionTypesTable::configure($table);
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
            'index' => ListCompanionTypes::route('/'),
            'create' => CreateCompanionType::route('/create'),
            'view' => ViewCompanionType::route('/{record}'),
            'edit' => EditCompanionType::route('/{record}/edit'),
        ];
    }
}
