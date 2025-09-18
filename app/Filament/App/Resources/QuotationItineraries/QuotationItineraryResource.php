<?php

namespace App\Filament\App\Resources\QuotationItineraries;

use App\Filament\App\Resources\QuotationItineraries\Pages\CreateQuotationItinerary;
use App\Filament\App\Resources\QuotationItineraries\Pages\EditQuotationItinerary;
use App\Filament\App\Resources\QuotationItineraries\Pages\ListQuotationItineraries;
use App\Filament\App\Resources\QuotationItineraries\Pages\ViewQuotationItinerary;
use App\Filament\App\Resources\QuotationItineraries\Schemas\QuotationItineraryForm;
use App\Filament\App\Resources\QuotationItineraries\Schemas\QuotationItineraryInfolist;
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

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'quotation_id';

    public static function form(Schema $schema): Schema
    {
        return QuotationItineraryForm::configure($schema);
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
            'edit' => EditQuotationItinerary::route('/{record}/edit'),
        ];
    }
}
