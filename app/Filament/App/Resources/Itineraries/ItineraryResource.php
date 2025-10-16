<?php

namespace App\Filament\App\Resources\Itineraries;

use App\Filament\App\Resources\Itineraries\Pages\CreateItinerary;
use App\Filament\App\Resources\Itineraries\Pages\EditItinerary;
use App\Filament\App\Resources\Itineraries\Pages\ListItineraries;
use App\Filament\App\Resources\Itineraries\Pages\ViewItinerary;
use App\Filament\App\Resources\Itineraries\Schemas\ItineraryForm;
use App\Filament\App\Resources\Itineraries\Schemas\ItineraryInfolist;
use App\Filament\App\Resources\Itineraries\Tables\ItinerariesTable;
use App\Models\Tenants\Itinerary;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ItineraryResource extends Resource
{
    protected static ?string $model = Itinerary::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = null;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    public static function getNavigationGroup(): ?string
    {
        return __('app-itineraries.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('app-itineraries.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('app-itineraries.resource_name');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app-itineraries.resource_name_plural');
    }

    public static function getRecordTitle($record): string
    {
        $travelMode = $record->travel_mode;
        if ($travelMode instanceof \App\Enums\TravelModeEnum) {
            return $travelMode->getLabel();
        }
        return (string) $travelMode;
    }

    public static function form(Schema $schema): Schema
    {
        return ItineraryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ItineraryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ItinerariesTable::configure($table);
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
            'index' => ListItineraries::route('/'),
            'edit' => EditItinerary::route('/{record}/edit'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false;
    }
}
