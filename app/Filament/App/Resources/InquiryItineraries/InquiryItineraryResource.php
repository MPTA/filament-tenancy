<?php

namespace App\Filament\App\Resources\InquiryItineraries;

use App\Filament\App\Resources\InquiryItineraries\Pages\ListInquiryItineraries;
use App\Filament\App\Resources\InquiryItineraries\Tables\InquiryItinerariesTable;
use App\Models\Tenants\InquiryItinerary;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InquiryItineraryResource extends Resource
{
    protected static ?string $model = InquiryItinerary::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Quotations';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Inquiry Itineraries';

    protected static ?string $recordTitleAttribute = 'date_type';

    public static function table(Table $table): Table
    {
        return InquiryItinerariesTable::configure($table);
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
            'index' => ListInquiryItineraries::route('/'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false;
    }
}
