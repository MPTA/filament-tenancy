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

    protected static ?int $navigationSort = 1;
    
    public static function getNavigationLabel(): string
    {
        return __('tenant-companion-types.navigation_label');
    }
    
    public static function getLabel(): ?string
    {
        return __('tenant-companion-types.resource_name');
    }
    
    public static function getPluralLabel(): ?string
    {
        return __('tenant-companion-types.resource_name_plural');
    }
    
    public static function getNavigationGroup(): ?string
    {
        return __('tenant-companion-types.navigation_group');
    }

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
        return $record->name . ' (' . ($record->companionCategory->name ?? __('tenant-companion-types.global_search.no_category')) . ')';
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            __('tenant-companion-types.global_search.category_label') => $record->companionCategory->name ?? __('tenant-companion-types.global_search.no_category'),
            __('tenant-companion-types.global_search.native_language_label') => $record->nativeLanguage->name ?? __('tenant-companion-types.global_search.not_specified'),
            __('tenant-companion-types.global_search.speaking_language_label') => $record->speakingLanguage->name ?? __('tenant-companion-types.global_search.not_specified'),
            __('tenant-companion-types.global_search.per_day_price_label') => $record->per_day_price ? '$' . number_format($record->per_day_price, 2) : __('tenant-companion-types.global_search.not_set'),
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
            ->with(['companionCategory', 'nativeLanguage', 'speakingLanguage']);
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
