<?php

namespace App\Filament\App\Resources\QuotationItineraries\Schemas;

use App\Enums\TravelModeEnum;
use App\Filament\App\Resources\Itineraries\ItineraryResource;
use App\Models\Tenants\Itinerary;
use App\Models\Tenants\QuotationItinerary;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use Illuminate\Support\Facades\Auth;

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
                            ->badge('✓')
                            ->badgeColor('success')
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
                                        Action::make('Create Itinerary')->hidden(fn( QuotationItinerary $quotationItinerary) => $quotationItinerary->itinerary)
                                        // ->url(fn() => ItineraryResource::getUrl('create'))
                                            ->size(Size::ExtraLarge)
                                            ->icon('heroicon-m-pencil-square')
                                            ->color('success')
                                            ->schema([
                                                Select::make('travel_mode')
                                                    ->options(TravelModeEnum::class)
                                            ])->action(function(array $data, QuotationItinerary $quotationItinerary){
                                                if (!$quotationItinerary->itinerary) {
                                                    $quotationItinerary->itinerary()->create([
                                                        'travel_mode' => $data['travel_mode'],
                                                        'creator_user_id' => Auth::user()->id,
                                                    ]);
                                                }
                                     
                                            })
                                    ])
                                    ->extraAttributes(['class' => 'flex justify-center items-center min-h-[200px]']),
                                    Grid::make(1)
                                    ->schema([
                                        Action::make('Edit Itinerary Days')->hidden(fn( QuotationItinerary $quotationItinerary) => !$quotationItinerary->itinerary)
                                            ->size(Size::ExtraLarge)
                                            ->icon('heroicon-m-pencil-square')
                                            ->color('primary')
                                    ])
                                    ->extraAttributes(['class' => 'flex justify-end items-center min-h-[200px]'])
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
