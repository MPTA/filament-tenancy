<?php

namespace App\Filament\App\Resources\QuotationItineraries\Schemas;

use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;

class QuotationItineraryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')->columnSpanFull()
                    ->tabs([
                        Tab::make('Information')
                            ->icon('heroicon-o-check-circle')
                            ->schema([
                                TextEntry::make('quotation.number'),
                                TextEntry::make('quotation.currency.name'),
                                TextEntry::make('quotation.exchange_rate'),
                                TextEntry::make('quotation.expire_date')->date()->label('Expire'),
                                TextEntry::make('quotation.creator.name')->label('Creator'),

                            ]),
                        Tab::make('Itinerary')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Grid::make(1)
                                    ->schema([
                                        Action::make('Create Itinerary')
                                            ->size(Size::ExtraLarge)->action(function(){
                                                
                                            })
                                            ->icon('heroicon-m-pencil-square')
                                    ])
                                    ->extraAttributes(['class' => 'flex justify-center items-center min-h-[200px]'])
                            ]),
                        Tab::make('Breakdown')
                            
                            ->schema([
                                // ...
                            ]),
                        Tab::make('Offers')
                            
                            ->schema([
                                // ...
                            ]),
                    ]),

                // Section::make('Offers')->schema([

                // ]),
                // Section::make('Breakdown')->schema([

                // ]),
                // Section::make('Itinerary')->schema([

                // ]),
                // Section::make('Information')->schema([

                //     ])
            ]);
    }
}
