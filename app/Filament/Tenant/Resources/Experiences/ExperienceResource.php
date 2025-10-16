<?php

namespace App\Filament\Tenant\Resources\Experiences;

use App\Filament\Tenant\Resources\Experiences\Pages\CreateExperience;
use App\Filament\Tenant\Resources\Experiences\Pages\EditExperience;
use App\Filament\Tenant\Resources\Experiences\Pages\ListExperiences;
use App\Filament\Tenant\Resources\Experiences\Pages\ViewExperience;
use App\Filament\Tenant\Resources\Experiences\Schemas\ExperienceForm;
use App\Filament\Tenant\Resources\Experiences\Schemas\ExperienceInfolist;
use App\Filament\Tenant\Resources\Experiences\Tables\ExperiencesTable;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use App\Models\Tenants\Experience;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ExperienceResource extends Resource
{
    use Translatable;
    protected static ?string $model = Experience::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;
    
    public static function getNavigationLabel(): string
    {
        return __('tenant-experiences.navigation_label');
    }
    
    public static function getLabel(): ?string
    {
        return __('tenant-experiences.resource_name');
    }
    
    public static function getPluralLabel(): ?string
    {
        return __('tenant-experiences.resource_name_plural');
    }
    
    public static function getNavigationGroup(): ?string
    {
        return __('tenant-experiences.navigation_group');
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
        return $record->name . ' (' . ($record->city->name ?? __('tenant-experiences.global_search.no_location')) . ')';
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            __('tenant-experiences.global_search.price_label') => $record->price ? '$' . number_format($record->price, 2) : __('tenant-experiences.global_search.free'),
            __('tenant-experiences.global_search.city_label') => $record->city->name ?? __('tenant-experiences.global_search.not_specified'),
            __('tenant-experiences.global_search.status_label') => $record->is_active ? __('tenant-experiences.global_search.active') : __('tenant-experiences.global_search.inactive'),
            __('tenant-experiences.global_search.charge_mode_label') => $record->charge_mode->value ?? __('tenant-experiences.global_search.not_specified'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description', 'address'];
    }

    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl('view', ['record' => $record]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['city', 'district', 'creator']);
    }

    public static function form(Schema $schema): Schema
    {
        return ExperienceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ExperienceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExperiencesTable::configure($table);
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
            'index' => ListExperiences::route('/'),
            'create' => CreateExperience::route('/create'),
            'view' => ViewExperience::route('/{record}'),
            'edit' => EditExperience::route('/{record}/edit'),
        ];
    }
}
