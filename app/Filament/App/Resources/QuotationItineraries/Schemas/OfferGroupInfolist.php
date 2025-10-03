<?php

namespace App\Filament\App\Resources\QuotationItineraries\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

class OfferGroupInfolist
{
    public static function getSchema(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    TextEntry::make('id')
                        ->label('Offer Group ID')
                        ->icon('heroicon-o-hashtag')
                        ->color('primary'),

                    TextEntry::make('created_at')
                        ->label('Created At')
                        ->dateTime()
                        ->icon('heroicon-o-calendar')
                        ->color('info'),
                ]),

            Section::make('Driver Settings')
                ->description('Driver cost configuration')
                ->icon('heroicon-o-user')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            IconEntry::make('is_include_driver_meal')
                                ->label('Include Driver Meal')
                                ->boolean()
                                ->icon(fn($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                                ->color(fn($state) => $state ? 'success' : 'gray'),

                            IconEntry::make('is_driver_same_meal')
                                ->label('Driver Same Meal')
                                ->boolean()
                                ->icon(fn($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                                ->color(fn($state) => $state ? 'success' : 'gray')
                                ->hidden(fn($record) => !$record->is_include_driver_meal),
                        ]),

                    Grid::make(2)
                        ->schema([
                            IconEntry::make('is_include_driver_hotel')
                                ->label('Include Driver Hotel')
                                ->boolean()
                                ->icon(fn($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                                ->color(fn($state) => $state ? 'success' : 'gray'),

                            IconEntry::make('is_driver_stay_same_hotel')
                                ->label('Driver Stays Same Hotel')
                                ->boolean()
                                ->icon(fn($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                                ->color(fn($state) => $state ? 'success' : 'gray')
                                ->hidden(fn($record) => !$record->is_include_driver_hotel),
                        ]),

                    TextEntry::make('driver_room_category_id')
                        ->label('Driver Room Type')
                        ->formatStateUsing(fn($state, $record) => 
                            $record->driverRoomCategory?->name ?? 'Not specified'
                        )
                        ->icon('heroicon-o-home')
                        ->color('primary')
                        ->hidden(fn($record) => !$record->is_driver_stay_same_hotel),
                ])
                ->collapsible()
                ->collapsed(false),

            RepeatableEntry::make('quotationOfferGroupCompanions')
                ->label('Companions Details')
                ->contained(false)
                ->schema([
                    Section::make()
                        ->heading(fn($record) => $record->companionType?->name ?? 'Unknown Companion')
                        ->description(function ($record) {
                            $info = [];
                            if ($record->is_same_meal) {
                                $info[] = 'Same meal as passengers';
                            }
                            if ($record->is_stay_same_hotel) {
                                $info[] = 'Stays in same hotel';
                            }
                            if ($record->livingCity) {
                                $info[] = 'Lives in: ' . $record->livingCity->name;
                            }
                            return implode(' • ', $info);
                        })
                        ->icon('heroicon-o-user')
                        ->schema([
                            // Basic Information
                            Grid::make(3)
                                ->schema([
                                    TextEntry::make('full_days_qty')
                                        ->label('Full Days')
                                        ->numeric()
                                        ->icon('heroicon-o-calendar')
                                        ->color('success'),

                                    TextEntry::make('half_days_qty')
                                        ->label('Half Days')
                                        ->numeric()
                                        ->icon('heroicon-o-clock')
                                        ->color('warning'),

                                    TextEntry::make('hours_qty')
                                        ->label('Hours')
                                        ->numeric()
                                        ->icon('heroicon-o-clock')
                                        ->color('info'),
                                ]),

                            // Pricing Information
                            Grid::make(3)
                                ->schema([
                                    TextEntry::make('day_price')
                                        ->label('Day Price')
                                        ->money('CNY')
                                        ->icon('heroicon-o-currency-dollar')
                                        ->color('success'),

                                    TextEntry::make('half_day_price')
                                        ->label('Half Day Price')
                                        ->money('CNY')
                                        ->icon('heroicon-o-currency-dollar')
                                        ->color('warning'),

                                    TextEntry::make('total_companion_salary')
                                        ->label('Total Companion Salary')
                                        ->money('CNY')
                                        ->icon('heroicon-o-currency-dollar')
                                        ->color('primary'),
                                ]),

                            // Meal Details
                            Section::make('Meal Details')
                                ->heading('Meal Details')
                                ->description(fn($record) => 'Total: ' . number_format($record->meal_cost, 2) . ' CNY - Detailed meal cost breakdown')
                                ->icon('heroicon-o-cake')
                                ->schema([
                                    RepeatableEntry::make('meals')
                                        ->label('Meal Records')
                                        ->hiddenLabel()
                                        ->contained(false)
                                        ->schema([
                                            Grid::make(4)
                                                ->schema([
                                                    TextEntry::make('mealType.name')
                                                        ->label('Meal Type')
                                                        ->formatStateUsing(fn($state) => $state ?? 'Base Budget')
                                                        ->icon('heroicon-o-cake')
                                                        ->color('primary'),

                                                    TextEntry::make('qty')
                                                        ->label('Quantity')
                                                        ->numeric()
                                                        ->icon('heroicon-o-hashtag')
                                                        ->color('info'),

                                                    TextEntry::make('price')
                                                        ->label('Unit Price')
                                                        ->money('CNY')
                                                        ->icon('heroicon-o-currency-dollar')
                                                        ->color('success'),

                                                    TextEntry::make('total_cost')
                                                        ->label('Total Price')
                                                        ->money('CNY')
                                                        ->icon('heroicon-o-calculator')
                                                        ->color('warning'),
                                                ])
                                        ])
                                        ->columns(1)
                                ])
                                ->collapsible()
                                ->collapsed(true),

                            // Ticket Details
                            Section::make('Ticket Details')
                                ->heading('Ticket Details')
                                ->description(fn($record) => 'Total: ' . number_format($record->ticket_cost, 2) . ' CNY - Transportation ticket cost breakdown')
                                ->icon('heroicon-o-ticket')
                                ->schema([
                                    RepeatableEntry::make('tickets')
                                        ->label('Ticket Records')
                                        ->hiddenLabel()
                                        ->contained(false)
                                        ->schema([
                                            Grid::make(4)
                                                ->schema([
                                                    TextEntry::make('fromCity.name')
                                                        ->label('From City')
                                                        ->icon('heroicon-o-map-pin')
                                                        ->color('primary'),

                                                    TextEntry::make('toCity.name')
                                                        ->label('To City')
                                                        ->icon('heroicon-o-map-pin')
                                                        ->color('info'),

                                                    TextEntry::make('class')
                                                        ->label('Class')
                                                        ->badge()
                                                        ->color('warning'),

                                                    TextEntry::make('price')
                                                        ->label('Price')
                                                        ->money('CNY')
                                                        ->icon('heroicon-o-currency-dollar')
                                                        ->color('success'),
                                                ])
                                        ])
                                        ->columns(1)
                                ])
                                ->collapsible()
                                ->collapsed(true),

                            // Experience Details
                            Section::make('Experience Details')
                                ->heading('Experience Details')
                                ->description(fn($record) => 'Total: ' . number_format($record->experience_cost, 2) . ' CNY - Experience cost breakdown')
                                ->icon('heroicon-o-sparkles')
                                ->schema([
                                    RepeatableEntry::make('experiences')
                                        ->label('Experience Records')
                                        ->hiddenLabel()
                                        ->contained(false)
                                        ->schema([
                                            Grid::make(2)
                                                ->schema([
                                                    TextEntry::make('experience.name')
                                                        ->label('Experience')
                                                        ->icon('heroicon-o-sparkles')
                                                        ->color('primary'),

                                                    TextEntry::make('price')
                                                        ->label('Price')
                                                        ->money('CNY')
                                                        ->icon('heroicon-o-currency-dollar')
                                                        ->color('success'),
                                                ])
                                        ])
                                        ->columns(1)
                                ])
                                ->collapsible()
                                ->collapsed(true),

                            // Attraction Details
                            Section::make('Attraction Details')
                                ->heading('Attraction Details')
                                ->description(fn($record) => 'Total: ' . number_format($record->attraction_cost, 2) . ' CNY - Attraction cost breakdown')
                                ->icon('heroicon-o-building-library')
                                ->schema([
                                    RepeatableEntry::make('attractions')
                                        ->label('Attraction Records')
                                        ->hiddenLabel()
                                        ->contained(false)
                                        ->schema([
                                            Grid::make(2)
                                                ->schema([
                                                    TextEntry::make('attraction.name')
                                                        ->label('Attraction')
                                                        ->icon('heroicon-o-building-library')
                                                        ->color('primary'),

                                                    TextEntry::make('price')
                                                        ->label('Price')
                                                        ->money('CNY')
                                                        ->icon('heroicon-o-currency-dollar')
                                                        ->color('success'),
                                                ]),

                                            RepeatableEntry::make('subAttractions')
                                                ->label('Sub Attractions')
                                                ->hiddenLabel()
                                                ->contained(false)
                                                ->schema([
                                                    Grid::make(2)
                                                        ->schema([
                                                            TextEntry::make('subAttraction.name')
                                                                ->label('Sub Attraction')
                                                                ->icon('heroicon-o-building-office')
                                                                ->color('info'),

                                                            TextEntry::make('price')
                                                                ->label('Price')
                                                                ->money('CNY')
                                                                ->icon('heroicon-o-currency-dollar')
                                                                ->color('success'),
                                                        ])
                                                ])
                                                ->columns(1)
                                        ])
                                        ->columns(1)
                                ])
                                ->collapsible()
                                ->collapsed(true),

                            // Expense Details
                            Section::make('Expense Details')
                                ->heading('Expense Details')
                                ->description(fn($record) => 'Total: ' . number_format($record->expense_cost, 2) . ' CNY - Additional expense breakdown')
                                ->icon('heroicon-o-document-text')
                                ->schema([
                                    RepeatableEntry::make('expenses')
                                        ->label('Expense Records')
                                        ->hiddenLabel()
                                        ->contained(false)
                                        ->schema([
                                            Grid::make(2)
                                                ->schema([
                                                    TextEntry::make('description')
                                                        ->label('Description')
                                                        ->icon('heroicon-o-document-text')
                                                        ->color('primary'),

                                                    TextEntry::make('price')
                                                        ->label('Price')
                                                        ->money('CNY')
                                                        ->icon('heroicon-o-currency-dollar')
                                                        ->color('success'),
                                                ])
                                        ])
                                        ->columns(1)
                                ])
                                ->collapsible()
                                ->collapsed(true),

                            // Accommodation Details
                            Section::make('Accommodation Details')
                                ->heading('Accommodation Details')
                                ->description(fn($record) => 'Total: ' . number_format($record->accommodation_cost, 2) . ' CNY - Accommodation cost breakdown')
                                ->icon('heroicon-o-home')
                                ->schema([
                                    RepeatableEntry::make('accommodations')
                                        ->label('Accommodation Records')
                                        ->hiddenLabel()
                                        ->contained(false)
                                        ->schema([
                                            Grid::make(4)
                                                ->schema([
                                                    TextEntry::make('accommodation.name')
                                                        ->label('Accommodation')
                                                        ->formatStateUsing(fn($state) => $state ?? 'Base Budget')
                                                        ->icon('heroicon-o-home')
                                                        ->color('primary'),

                                                    TextEntry::make('city.name')
                                                        ->label('City')
                                                        ->icon('heroicon-o-map-pin')
                                                        ->color('info'),

                                                    TextEntry::make('nights')
                                                        ->label('Nights')
                                                        ->numeric()
                                                        ->icon('heroicon-o-moon')
                                                        ->color('warning'),

                                                    TextEntry::make('night_price')
                                                        ->label('Night Price')
                                                        ->money('CNY')
                                                        ->icon('heroicon-o-currency-dollar')
                                                        ->color('success'),
                                                ]),

                                            TextEntry::make('total_cost')
                                                ->label('Total Accommodation Price')
                                                ->money('CNY')
                                                ->icon('heroicon-o-calculator')
                                                ->color('warning')
                                                ->columnSpanFull(),
                                        ])
                                        ->columns(1)
                                ])
                                ->collapsible()
                                ->collapsed(true),

                            // Summary
                            Section::make('Cost Summary')
                                ->description('Total cost breakdown for this companion')
                                ->icon('heroicon-o-calculator')
                                ->schema([
                                    // Total Cost
                                    TextEntry::make('total_companion_cost')
                                        ->label('Total Companion Cost')
                                        ->formatStateUsing(fn($state, $record) => 
                                            number_format(
                                                $record->meal_cost + 
                                                $record->experience_cost + 
                                                $record->attraction_cost + 
                                                $record->expense_cost + 
                                                $record->accommodation_cost + 
                                                $record->ticket_cost + 
                                                ($record->full_days_qty * $record->day_price) + 
                                                ($record->half_days_qty * $record->half_day_price), 2
                                            )
                                        )
                                        ->money('CNY')
                                        ->icon('heroicon-o-currency-dollar')
                                        ->color('success')
                                        ->weight('bold')
                                        ->size('lg'),

                                    // Cost Breakdown Table
                                    Grid::make(2)
                                        ->schema([
                                            // Left Column - Categories
                                            TextEntry::make('total_companion_salary')
                                                ->label(fn($record) => 'Salary (' . $record->full_days_qty . ' full days + ' . $record->half_days_qty . ' half days)')
                                                ->money('CNY')
                                                ->icon('heroicon-o-currency-dollar')
                                                ->color('success'),

                                            TextEntry::make('meal_cost')
                                                ->label('Meals')
                                                ->money('CNY')
                                                ->icon('heroicon-o-cake')
                                                ->color('success'),

                                            TextEntry::make('experience_cost')
                                                ->label('Experiences')
                                                ->money('CNY')
                                                ->icon('heroicon-o-sparkles')
                                                ->color('success'),

                                            TextEntry::make('attraction_cost')
                                                ->label('Attractions')
                                                ->money('CNY')
                                                ->icon('heroicon-o-building-library')
                                                ->color('success'),

                                            TextEntry::make('expense_cost')
                                                ->label('Expenses')
                                                ->money('CNY')
                                                ->icon('heroicon-o-document-text')
                                                ->color('success'),

                                            TextEntry::make('accommodation_cost')
                                                ->label('Accommodation')
                                                ->money('CNY')
                                                ->icon('heroicon-o-home')
                                                ->color('success'),

                                            TextEntry::make('ticket_cost')
                                                ->label('Tickets')
                                                ->money('CNY')
                                                ->icon('heroicon-o-ticket')
                                                ->color('success'),
                                        ])
                                ])
                                ->collapsible(false),
                        ])
                ])
                ->columns(1),
        ];
    }
}
