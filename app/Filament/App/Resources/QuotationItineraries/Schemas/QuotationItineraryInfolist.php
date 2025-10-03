<?php

namespace App\Filament\App\Resources\QuotationItineraries\Schemas;

use App\Models\Tenants\QuotationItinerary;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class QuotationItineraryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')->columnSpanFull()
                    ->persistTabInQueryString()
                    ->tabs([
                        InformationTab::getTab(),
                        ItineraryTab::getTab(),
                        BreakdownTab::getTab(),
                        OffersTab::getTab(),
                    ]),
            ]);
    }




}
