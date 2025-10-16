<?php

namespace App\Filament\App\Resources\QuotationItineraries;

use App\Filament\App\Resources\QuotationItineraries\Pages\CreateQuotationItinerary;
use App\Filament\App\Resources\QuotationItineraries\Pages\CustomerView;
use App\Filament\App\Resources\QuotationItineraries\Pages\EditBreakdown;
use App\Filament\App\Resources\QuotationItineraries\Pages\ListQuotationItineraries;
use App\Filament\App\Resources\QuotationItineraries\Pages\ViewQuotationItinerary;
use App\Filament\App\Resources\QuotationItineraries\Schemas\QuotationItineraryForm;
use App\Filament\App\Resources\QuotationItineraries\Schemas\QuotationItineraryInfolist;
use App\Filament\App\Resources\QuotationItineraries\Schemas\BreakdownForm;
use App\Filament\App\Resources\QuotationItineraries\Tables\QuotationItinerariesTable;
use App\Models\Tenants\QuotationItinerary;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class QuotationItineraryResource extends Resource
{
    protected static ?string $model = QuotationItinerary::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = null;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static ?string $recordTitleAttribute = 'quotation_id';

    public static function getNavigationGroup(): ?string
    {
        return __('app-quotation-itineraries.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('app-quotation-itineraries.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('app-quotation-itineraries.resource_name');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app-quotation-itineraries.resource_name_plural');
    }

    public static function form(Schema $schema): Schema
    {
        return QuotationItineraryForm::configure($schema);
    }

    public static function breakdownForm(Schema $schema): Schema
    {
        return BreakdownForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return QuotationItineraryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuotationItinerariesTable::configure($table);
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
            'index' => ListQuotationItineraries::route('/'),
            'create' => CreateQuotationItinerary::route('/create'),
            'view' => ViewQuotationItinerary::route('/{record}'),
            'edit-breakdown' => EditBreakdown::route('/{record}/edit-breakdown'),
            'customer-view' => CustomerView::route('/{record}/customer-view'),
        ];
    }
}
