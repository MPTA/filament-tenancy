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
                    TextEntry::make('full_number')
                        ->label(__('app-quotation-itineraries.fields.offer_group_number'))
                        ->icon('heroicon-o-hashtag')
                        ->color('primary')
                        ->weight('bold')
                        ->size('lg'),

                    TextEntry::make('created_at')
                        ->label(__('app-quotation-itineraries.fields.created_at'))
                        ->dateTime()
                        ->icon('heroicon-o-calendar')
                        ->color('info'),
                ]),

            Section::make(__('app-quotation-itineraries.sections.driver_settings_infolist.title'))
                ->description(__('app-quotation-itineraries.sections.driver_settings_infolist.description'))
                ->icon('heroicon-o-user')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            IconEntry::make('is_include_driver_meal')
                                ->label(__('app-quotation-itineraries.fields.include_driver_meal'))
                                ->boolean()
                                ->icon(fn($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                                ->color(fn($state) => $state ? 'success' : 'gray'),

                            IconEntry::make('is_driver_same_meal')
                                ->label(__('app-quotation-itineraries.fields.driver_same_meal'))
                                ->boolean()
                                ->icon(fn($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                                ->color(fn($state) => $state ? 'success' : 'gray')
                                ->hidden(fn($record) => !$record->is_include_driver_meal),
                        ]),

                    Grid::make(2)
                        ->schema([
                            IconEntry::make('is_include_driver_hotel')
                                ->label(__('app-quotation-itineraries.fields.include_driver_hotel'))
                                ->boolean()
                                ->icon(fn($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                                ->color(fn($state) => $state ? 'success' : 'gray'),

                            IconEntry::make('is_driver_stay_same_hotel')
                                ->label(__('app-quotation-itineraries.fields.driver_stays_same_hotel'))
                                ->boolean()
                                ->icon(fn($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                                ->color(fn($state) => $state ? 'success' : 'gray')
                                ->hidden(fn($record) => !$record->is_include_driver_hotel),
                        ]),

                    TextEntry::make('driver_room_category_id')
                        ->label(__('app-quotation-itineraries.fields.driver_room_type'))
                        ->formatStateUsing(fn($state, $record) => 
                            $record->driverRoomCategory?->name ?? __('app-quotation-itineraries.placeholders.not_specified')
                        )
                        ->icon('heroicon-o-home')
                        ->color('primary')
                        ->hidden(fn($record) => !$record->is_driver_stay_same_hotel),
                ])
                ->collapsible()
                ->collapsed(false),

            RepeatableEntry::make('quotationOfferGroupCompanions')
                ->label(__('app-quotation-itineraries.sections.companions_details.title'))
                ->contained(false)
                ->schema([
                    Section::make()
                        ->heading(fn($record) => $record->companionType?->name ?? __('app-quotation-itineraries.infolist_placeholders.unknown_companion'))
                        ->description(function ($record) {
                            $info = [];
                            if ($record->is_same_meal) {
                                $info[] = __('app-quotation-itineraries.infolist_placeholders.same_meal_as_passengers');
                            }
                            if ($record->is_stay_same_hotel) {
                                $info[] = __('app-quotation-itineraries.infolist_placeholders.stays_in_same_hotel');
                            }
                            if ($record->livingCity) {
                                $info[] = __('app-quotation-itineraries.infolist_placeholders.lives_in', ['city' => $record->livingCity->name]);
                            }
                            return implode(' • ', $info);
                        })
                        ->icon('heroicon-o-user')
                        ->schema([
                            // Basic Information
                            Grid::make(3)
                                ->schema([
                                    TextEntry::make('full_days_qty')
                                        ->label(__('app-quotation-itineraries.fields.full_days'))
                                        ->numeric()
                                        ->icon('heroicon-o-calendar')
                                        ->color('success'),

                                    TextEntry::make('half_days_qty')
                                        ->label(__('common-fields.half_days'))
                                        ->numeric()
                                        ->icon('heroicon-o-clock')
                                        ->color('warning'),

                                    TextEntry::make('hours_qty')
                                        ->label(__('app-quotation-itineraries.fields.hours'))
                                        ->numeric()
                                        ->icon('heroicon-o-clock')
                                        ->color('info'),
                                ]),

                            // Pricing Information
                            Grid::make(3)
                                ->schema([
                                    TextEntry::make('day_price')
                                        ->label(__('app-quotation-itineraries.fields.day_price'))
                                        ->money('CNY')
                                        ->icon('heroicon-o-currency-dollar')
                                        ->color('success'),

                                    TextEntry::make('half_day_price')
                                        ->label(__('common-fields.half_day_price'))
                                        ->money('CNY')
                                        ->icon('heroicon-o-currency-dollar')
                                        ->color('warning'),

                                    TextEntry::make('total_companion_salary')
                                        ->label(__('app-quotation-itineraries.fields.total_companion_salary'))
                                        ->money('CNY')
                                        ->icon('heroicon-o-currency-dollar')
                                        ->color('primary'),
                                ]),

                            // Meal Details
                            Section::make(__('app-quotation-itineraries.sections.meal_details.title'))
                                ->heading(__('app-quotation-itineraries.sections.meal_details.title'))
                                ->description(fn($record) => __('app-quotation-itineraries.sections.meal_details.description', ['total' => number_format($record->meal_cost, 2)]))
                                ->icon('heroicon-o-cake')
                                ->schema([
                                    RepeatableEntry::make('meals')
                                        ->label(__('app-quotation-itineraries.fields.meal_records'))
                                        ->hiddenLabel()
                                        ->contained(false)
                                        ->schema([
                                            Grid::make(4)
                                                ->schema([
                                                    TextEntry::make('mealType.name')
                                                        ->label(__('common-fields.meal_type'))
                                                        ->formatStateUsing(fn($state) => $state ?? __('app-quotation-itineraries.infolist_placeholders.base_budget'))
                                                        ->icon('heroicon-o-cake')
                                                        ->color('primary'),

                                                    TextEntry::make('qty')
                                                        ->label(__('app-quotation-itineraries.fields.quantity'))
                                                        ->numeric()
                                                        ->icon('heroicon-o-hashtag')
                                                        ->color('info'),

                                                    TextEntry::make('price')
                                                        ->label(__('app-quotation-itineraries.fields.unit_price'))
                                                        ->money('CNY')
                                                        ->icon('heroicon-o-currency-dollar')
                                                        ->color('success'),

                                                    TextEntry::make('total_cost')
                                                        ->label(__('app-quotation-itineraries.fields.total_price'))
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
                            Section::make(__('app-quotation-itineraries.sections.ticket_details.title'))
                                ->heading(__('app-quotation-itineraries.sections.ticket_details.title'))
                                ->description(fn($record) => __('app-quotation-itineraries.sections.ticket_details.description', ['total' => number_format($record->ticket_cost, 2)]))
                                ->icon('heroicon-o-ticket')
                                ->schema([
                                    RepeatableEntry::make('tickets')
                                        ->label(__('app-quotation-itineraries.fields.ticket_records'))
                                        ->hiddenLabel()
                                        ->contained(false)
                                        ->schema([
                                            Grid::make(4)
                                                ->schema([
                                                    TextEntry::make('fromCity.name')
                                                        ->label(__('common-fields.from_city'))
                                                        ->icon('heroicon-o-map-pin')
                                                        ->color('primary'),

                                                    TextEntry::make('toCity.name')
                                                        ->label(__('common-fields.to_city'))
                                                        ->icon('heroicon-o-map-pin')
                                                        ->color('info'),

                                                    TextEntry::make('class')
                                                        ->label(__('common-fields.class'))
                                                        ->badge()
                                                        ->color('warning'),

                                                    TextEntry::make('price')
                                                        ->label(__('common-fields.price'))
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
                            Section::make(__('app-quotation-itineraries.sections.experience_details.title'))
                                ->heading(__('app-quotation-itineraries.sections.experience_details.title'))
                                ->description(fn($record) => __('app-quotation-itineraries.sections.experience_details.description', ['total' => number_format($record->experience_cost, 2)]))
                                ->icon('heroicon-o-sparkles')
                                ->schema([
                                    RepeatableEntry::make('experiences')
                                        ->label(__('app-quotation-itineraries.fields.experience_records'))
                                        ->hiddenLabel()
                                        ->contained(false)
                                        ->schema([
                                            Grid::make(2)
                                                ->schema([
                                                    TextEntry::make('experience.name')
                                                        ->label(__('common-fields.experience'))
                                                        ->icon('heroicon-o-sparkles')
                                                        ->color('primary'),

                                                    TextEntry::make('price')
                                                        ->label(__('common-fields.price'))
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
                            Section::make(__('app-quotation-itineraries.sections.attraction_details.title'))
                                ->heading(__('app-quotation-itineraries.sections.attraction_details.title'))
                                ->description(fn($record) => __('app-quotation-itineraries.sections.attraction_details.description', ['total' => number_format($record->attraction_cost, 2)]))
                                ->icon('heroicon-o-building-library')
                                ->schema([
                                    RepeatableEntry::make('attractions')
                                        ->label(__('app-quotation-itineraries.fields.attraction_records'))
                                        ->hiddenLabel()
                                        ->contained(false)
                                        ->schema([
                                            Grid::make(3)
                                                ->schema([
                                                    TextEntry::make('attraction.name')
                                                        ->label('🏛️ ' . __('common-fields.attraction'))
                                                        ->weight('bold')
                                                        ->color('primary')
                                                        ->columnSpan(1),

                                                    TextEntry::make('price')
                                                        ->label('💵 ' . __('common-fields.entry_price'))
                                                        ->formatStateUsing(function ($state) {
                                                            return 'CNY ' . number_format($state, 2);
                                                        })
                                                        ->badge()
                                                        ->color('warning')
                                                        ->columnSpan(1),

                                                    TextEntry::make('id')
                                                        ->label('🎫 ' . __('common-fields.sub_attractions'))
                                                        ->formatStateUsing(fn($state, $record) => 
                                                            $record->subAttractions && $record->subAttractions->isNotEmpty()
                                                                ? nl2br(e(
                                                                    $record->subAttractions->map(function ($subAttraction) {
                                                                        $name = $subAttraction->subAttraction?->name ?? __('app-quotation-itineraries.placeholders.unknown');
                                                                        $price = number_format($subAttraction->price, 2);
                                                                        return "• {$name}: CNY {$price}";
                                                                    })->implode("\n")
                                                                ))
                                                                : __('app-quotation-itineraries.breakdown_placeholders.no_sub_attractions')
                                                        )
                                                        ->html()
                                                        ->color('info')
                                                        ->columnSpan(1),
                                                ])
                                        ])
                                ])
                                ->collapsible()
                                ->collapsed(true),

                            // Expense Details
                            Section::make(__('app-quotation-itineraries.sections.expense_details.title'))
                                ->heading(__('app-quotation-itineraries.sections.expense_details.title'))
                                ->description(fn($record) => __('app-quotation-itineraries.sections.expense_details.description', ['total' => number_format($record->expense_cost, 2)]))
                                ->icon('heroicon-o-document-text')
                                ->schema([
                                    RepeatableEntry::make('expenses')
                                        ->label(__('app-quotation-itineraries.fields.expense_records'))
                                        ->hiddenLabel()
                                        ->contained(false)
                                        ->schema([
                                            Grid::make(2)
                                                ->schema([
                                                    TextEntry::make('description')
                                                        ->label(__('common-fields.description'))
                                                        ->icon('heroicon-o-document-text')
                                                        ->color('primary'),

                                                    TextEntry::make('price')
                                                        ->label(__('common-fields.price'))
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
                            Section::make(__('app-quotation-itineraries.sections.accommodation_details.title'))
                                ->heading(__('app-quotation-itineraries.sections.accommodation_details.title'))
                                ->description(fn($record) => __('app-quotation-itineraries.sections.accommodation_details.description', ['total' => number_format($record->accommodation_cost, 2)]))
                                ->icon('heroicon-o-home')
                                ->schema([
                                    RepeatableEntry::make('accommodations')
                                        ->label(__('app-quotation-itineraries.fields.accommodation_records'))
                                        ->hiddenLabel()
                                        ->contained(false)
                                        ->schema([
                                            Grid::make(4)
                                                ->schema([
                                                    TextEntry::make('accommodation.name')
                                                        ->label('🏨 ' . __('common-fields.hotel'))
                                                        ->formatStateUsing(fn($state, $record) => 
                                                            $state 
                                                                ? $state . ($record->city?->name ? ' (' . $record->city->name . ')' : '')
                                                                : __('app-quotation-itineraries.infolist_placeholders.base_budget')
                                                        )
                                                        ->weight('bold')
                                                        ->color('primary')
                                                        ->columnSpan(1),

                                                    TextEntry::make('nights')
                                                        ->label('🌙 ' . __('common-fields.nights'))
                                                        ->numeric()
                                                        ->badge()
                                                        ->color('warning')
                                                        ->columnSpan(1),

                                                    TextEntry::make('night_price')
                                                        ->label('💵 ' . __('app-quotation-itineraries.fields.per_night'))
                                                        ->formatStateUsing(function ($state) {
                                                            return 'CNY ' . number_format($state, 2);
                                                        })
                                                        ->badge()
                                                        ->color('success')
                                                        ->columnSpan(1),

                                                    TextEntry::make('total_cost')
                                                        ->label('💰 ' . __('app-quotation-itineraries.fields.total_cost'))
                                                        ->formatStateUsing(function ($state, $record) {
                                                            $total = $record->nights * $record->night_price;
                                                            return 'CNY ' . number_format($total, 2);
                                                        })
                                                        ->badge()
                                                        ->color('info')
                                                        ->columnSpan(1),
                                                ])
                                        ])
                                ])
                                ->collapsible()
                                ->collapsed(true),

                            // Summary
                            Section::make(__('app-quotation-itineraries.sections.cost_summary.title'))
                                ->description(__('app-quotation-itineraries.sections.cost_summary.description'))
                                ->icon('heroicon-o-calculator')
                                ->schema([
                                    // Total Cost
                                    TextEntry::make('total_companion_cost')
                                        ->label(__('app-quotation-itineraries.fields.total_companion_cost'))
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
                                                ->label(fn($record) => __('app-quotation-itineraries.infolist_messages.salary_days', ['full' => $record->full_days_qty, 'half' => $record->half_days_qty]))
                                                ->money('CNY')
                                                ->icon('heroicon-o-currency-dollar')
                                                ->color('success'),

                                            TextEntry::make('meal_cost')
                                                ->label(__('common-fields.meals'))
                                                ->money('CNY')
                                                ->icon('heroicon-o-cake')
                                                ->color('success'),

                                            TextEntry::make('experience_cost')
                                                ->label(__('common-fields.experiences'))
                                                ->money('CNY')
                                                ->icon('heroicon-o-sparkles')
                                                ->color('success'),

                                            TextEntry::make('attraction_cost')
                                                ->label(__('common-fields.attractions'))
                                                ->money('CNY')
                                                ->icon('heroicon-o-building-library')
                                                ->color('success'),

                                            TextEntry::make('expense_cost')
                                                ->label(__('common-fields.expenses'))
                                                ->money('CNY')
                                                ->icon('heroicon-o-document-text')
                                                ->color('success'),

                                            TextEntry::make('accommodation_cost')
                                                ->label(__('app-quotation-itineraries.fields.accommodation'))
                                                ->money('CNY')
                                                ->icon('heroicon-o-home')
                                                ->color('success'),

                                            TextEntry::make('ticket_cost')
                                                ->label(__('common-fields.tickets'))
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
