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

    protected static ?string $navigationLabel = 'Experiences';

    protected static ?string $modelLabel = 'Experience';

    protected static ?string $pluralModelLabel = 'Experiences';

    protected static string | UnitEnum | null $navigationGroup = 'Content';

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
        return $record->name . ' (' . ($record->city->name ?? 'No Location') . ')';
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Price' => $record->price ? '$' . number_format($record->price, 2) : 'Free',
            'City' => $record->city->name ?? 'Not specified',
            'Status' => $record->is_active ? 'Active' : 'Inactive',
            'Charge Mode' => $record->charge_mode->value ?? 'Not specified',
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
            ->with(['city', 'district', 'currency', 'creator']);
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
