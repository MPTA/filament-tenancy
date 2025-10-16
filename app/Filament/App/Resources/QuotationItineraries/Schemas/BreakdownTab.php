<?php

namespace App\Filament\App\Resources\QuotationItineraries\Schemas;

use App\Models\Tenants\QuotationItinerary;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\Size;

class BreakdownTab
{
    public static function getTab(): Tab
    {
        return Tab::make(__('app-quotation-itineraries.tabs.breakdown'))
            ->icon('heroicon-o-calculator')
            ->badge(function (QuotationItinerary $record) {
                if (!$record->breakdown) {
                    return null; // No badge when no breakdown
                }

                if ($record->breakdown->is_completed) {
                    return '✓'; // Green tick when complete
                }

                return '⏳'; // Pending symbol when incomplete
            })
            ->badgeColor(function (QuotationItinerary $record) {
                if (!$record->breakdown) {
                    return 'gray';
                }

                if ($record->breakdown->is_completed) {
                    return 'success';
                }

                return 'gray'; // Gray for pending
            })
            ->schema([
                self::createBreakdownSection(),
                self::breakdownOverviewSection(),
                self::vehicleTypesSection(),
                self::ticketsSection(),
                self::mealsSection(),
                self::experiencesSection(),
                self::accommodationsSection(),
                self::attractionsSection(),
                self::companionsSection(),
                self::expensesSection(),
            ]);
    }

    private static function createBreakdownSection(): Section
    {
        return Section::make(__('app-quotation-itineraries.sections.create_breakdown.title'))
            ->description(__('app-quotation-itineraries.sections.create_breakdown.description'))
            ->hidden(fn(QuotationItinerary $quotationItinerary) => $quotationItinerary->breakdown)
            ->schema([
                Grid::make(1)
                    ->schema([
                        Action::make(__('app-quotation-itineraries.actions.create_breakdown'))
                            ->size(Size::ExtraLarge)
                            ->icon('heroicon-m-plus-circle')
                            ->color(fn(QuotationItinerary $quotationItinerary) => 
                                $quotationItinerary->itinerary?->is_complete ? 'primary' : 'gray'
                            )
                            ->disabled(fn(QuotationItinerary $quotationItinerary) => 
                                !$quotationItinerary->itinerary?->is_complete
                            )
                            ->label(fn(QuotationItinerary $quotationItinerary) => 
                                $quotationItinerary->itinerary?->is_complete 
                                    ? __('app-quotation-itineraries.actions.create_breakdown')
                                    : __('app-quotation-itineraries.actions.complete_itinerary_first')
                            )
                            ->tooltip(fn(QuotationItinerary $quotationItinerary) => 
                                !$quotationItinerary->itinerary?->is_complete 
                                    ? __('app-quotation-itineraries.tooltips_breakdown.complete_itinerary_before_breakdown')
                                    : null
                            )
                            ->action(function (QuotationItinerary $quotationItinerary) {
                                // Check if itinerary is complete
                                if (!$quotationItinerary->itinerary || !$quotationItinerary->itinerary->is_complete) {
                                    Notification::make()
                                        ->title(__('app-quotation-itineraries.notifications.incomplete_itinerary_title'))
                                        ->body(__('app-quotation-itineraries.notifications.incomplete_itinerary_body'))
                                        ->warning()
                                        ->send();
                                    return;
                                }

                                // Generate breakdown from itinerary
                                $quotationItinerary->generateBreakdownFromItinerary();

                                // Refresh the record to update the UI
                                $quotationItinerary->refresh();

                                Notification::make()
                                    ->title(__('app-quotation-itineraries.notifications.breakdown_generated_title'))
                                    ->body(__('app-quotation-itineraries.notifications.breakdown_generated_body'))
                                    ->success()
                                    ->send();

                                // Redirect to breakdown edit form
                                return redirect()->to(\App\Filament\App\Resources\QuotationItineraries\QuotationItineraryResource::getUrl('edit-breakdown', ['record' => $quotationItinerary]));
                            })
                            ->modalHeading(__('app-quotation-itineraries.modals_breakdown.create_breakdown_heading'))
                            ->modalDescription(__('app-quotation-itineraries.modals_breakdown.create_breakdown_description'))
                            ->modalSubmitActionLabel(__('app-quotation-itineraries.actions.create_breakdown'))
                    ])
                    ->extraAttributes(['class' => 'flex justify-center items-center min-h-[200px]'])
            ])
            ->collapsible(false);
    }

    private static function breakdownOverviewSection(): Section
    {
        return Section::make(__('app-quotation-itineraries.sections.breakdown_overview.title'))
            ->description(__('app-quotation-itineraries.sections.breakdown_overview.description'))
            ->hidden(fn(QuotationItinerary $quotationItinerary) => !$quotationItinerary->breakdown)
            ->headerActions([
                self::regenerateBreakdownAction(),
                self::completeBreakdownAction(),
                self::editBreakdownAction(),
                self::deleteBreakdownAction(),
            ])
            ->schema([
                Grid::make(4)
                    ->schema([
                        TextEntry::make('breakdown.is_completed')
                            ->label(__('common-fields.status'))
                            ->formatStateUsing(fn($state) => $state ? __('app-quotation-itineraries.status_labels.completed') : __('app-quotation-itineraries.status_labels.in_progress'))
                            ->icon(fn($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-clock')
                            ->color(fn($state) => $state ? 'success' : 'warning')
                            ->columnStart(1),
                    ]),

                Grid::make(4)
                    ->schema([
                        TextEntry::make('breakdown.vehicle_days_qty')
                            ->label(__('app-quotation-itineraries.fields.vehicle_days'))
                            ->numeric()
                            ->icon('heroicon-o-truck')
                            ->color('primary'),

                        TextEntry::make('breakdown.vehicle_half_days_qty')
                            ->label(__('app-quotation-itineraries.fields.half_days'))
                            ->numeric()
                            ->icon('heroicon-o-clock')
                            ->color('warning'),

                        TextEntry::make('breakdown.vehicle_hours_qty')
                            ->label(__('app-quotation-itineraries.fields.vehicle_hours'))
                            ->numeric()
                            ->icon('heroicon-o-clock')
                            ->color('info'),

                        TextEntry::make('breakdown.currency.name')
                            ->label(__('common-fields.currency'))
                            ->icon('heroicon-o-banknotes')
                            ->color('success'),
                    ]),

                Grid::make(2)
                    ->schema([
                        TextEntry::make('breakdown.driver_base_meal_budget')
                            ->label(__('app-quotation-itineraries.fields.driver_meal_budget'))
                            ->money('CNY')
                            ->icon('heroicon-o-currency-dollar')
                            ->color('success'),

                        TextEntry::make('breakdown.driver_base_accommodation_budget')
                            ->label(__('app-quotation-itineraries.fields.driver_accommodation_budget'))
                            ->money('CNY')
                            ->icon('heroicon-o-home')
                            ->color('primary'),

                        TextEntry::make('breakdown.companion_base_meal_budget')
                            ->label(__('app-quotation-itineraries.fields.companion_meal_budget'))
                            ->money('CNY')
                            ->icon('heroicon-o-currency-dollar')
                            ->color('warning'),

                        TextEntry::make('breakdown.companion_base_accommodation_budget')
                            ->label(__('app-quotation-itineraries.fields.companion_accommodation_budget'))
                            ->money('CNY')
                            ->icon('heroicon-o-home')
                            ->color('info'),
                    ]),
            ])
            ->collapsible(false);
    }

    private static function vehicleTypesSection(): Section
    {
        return Section::make(__('app-quotation-itineraries.sections.vehicle_types.title'))
            ->description(__('app-quotation-itineraries.sections.vehicle_types.description'))
            ->hidden(fn(QuotationItinerary $quotationItinerary) => !$quotationItinerary->breakdown)
            ->schema([
                RepeatableEntry::make('breakdown.vehicleTypes')
                    ->hiddenLabel()
                    ->contained(false)
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('vehicleType.name')
                                    ->label(__('app-quotation-itineraries.fields.vehicle_type'))
                                    ->icon('heroicon-o-truck')
                                    ->color('primary'),

                                TextEntry::make('per_day_price')
                                    ->label(__('app-quotation-itineraries.fields.per_day_price'))
                                    ->money('CNY')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->color('success'),

                                TextEntry::make('half_day_price')
                                    ->label(__('app-quotation-itineraries.fields.half_day_price'))
                                    ->money('CNY')
                                    ->icon('heroicon-o-clock')
                                    ->color('warning'),
                            ])
                    ])
            ])
            ->collapsible();
    }

    private static function ticketsSection(): Section
    {
        return Section::make(__('app-quotation-itineraries.sections.tickets.title'))
            ->description(__('app-quotation-itineraries.sections.tickets.description'))
            ->hidden(fn(QuotationItinerary $quotationItinerary) => !$quotationItinerary->breakdown)
            ->schema([
                RepeatableEntry::make('breakdown.tickets')
                    ->hiddenLabel()
                    ->contained(false)
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('transport_mode')
                                    ->label(__('app-quotation-itineraries.fields.transport_mode'))
                                    ->badge()
                                    ->color('primary'),

                                TextEntry::make('fromCity.name')
                                    ->label(__('app-quotation-itineraries.fields.from_city'))
                                    ->icon('heroicon-o-map-pin')
                                    ->color('success'),

                                TextEntry::make('toCity.name')
                                    ->label(__('app-quotation-itineraries.fields.to_city'))
                                    ->icon('heroicon-o-map-pin')
                                    ->color('warning'),

                                TextEntry::make('class')
                                    ->label(__('app-quotation-itineraries.fields.class'))
                                    ->badge()
                                    ->color('info'),
                            ]),

                        TextEntry::make('price')
                            ->label(__('app-quotation-itineraries.fields.price'))
                            ->money('CNY')
                            ->icon('heroicon-o-currency-dollar')
                            ->color('success')
                            ->columnSpanFull(),
                    ])
            ])
            ->collapsible();
    }

    private static function mealsSection(): Section
    {
        return Section::make(__('app-quotation-itineraries.sections.meals.title'))
            ->description(__('app-quotation-itineraries.sections.meals.description'))
            ->hidden(fn(QuotationItinerary $quotationItinerary) => !$quotationItinerary->breakdown)
            ->schema([
                RepeatableEntry::make('breakdown.meals')
                    ->hiddenLabel()
                    ->contained(false)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('mealType.name')
                                    ->label(__('app-quotation-itineraries.fields.meal_type'))
                                    ->icon('heroicon-o-cake')
                                    ->color('primary'),

                                TextEntry::make('price')
                                    ->label(__('app-quotation-itineraries.fields.price'))
                                    ->money('CNY')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->color('warning'),
                            ])
                    ])
            ])
            ->collapsible();
    }

    private static function experiencesSection(): Section
    {
        return Section::make(__('app-quotation-itineraries.sections.experiences.title'))
            ->description(__('app-quotation-itineraries.sections.experiences.description'))
            ->hidden(fn(QuotationItinerary $quotationItinerary) => !$quotationItinerary->breakdown)
            ->schema([
                RepeatableEntry::make('breakdown.experiences')
                    ->hiddenLabel()
                    ->contained(false)
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('experience.name')
                                    ->label(__('common-fields.experience'))
                                    ->icon('heroicon-o-sparkles')
                                    ->color('primary'),

                                TextEntry::make('price')
                                    ->label(__('app-quotation-itineraries.fields.price'))
                                    ->money('CNY')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->color('success'),

                                TextEntry::make('charge_mode')
                                    ->label(__('app-quotation-itineraries.fields.charge_mode'))
                                    ->formatStateUsing(fn($state) => $state?->label() ?? $state)
                                    ->badge()
                                    ->color('info'),
                            ])
                    ])
            ])
            ->collapsible();
    }

    private static function accommodationsSection(): Section
    {
        return Section::make(__('app-quotation-itineraries.sections.accommodations.title'))
            ->description(__('app-quotation-itineraries.sections.accommodations.description'))
            ->hidden(fn(QuotationItinerary $quotationItinerary) => !$quotationItinerary->breakdown)
            ->schema([
                RepeatableEntry::make('breakdown.accommodations')
                    ->hiddenLabel()
                    ->contained(false)
                    ->schema([
                        Grid::make(5)
                            ->schema([
                                TextEntry::make('accommodation.name')
                                    ->label('🏨 ' . __('app-quotation-itineraries.fields.hotel'))
                                    ->weight('bold')
                                    ->color('primary')
                                    ->columnSpan(1),

                                TextEntry::make('city.name')
                                    ->label('📍 ' . __('common-fields.city'))
                                    ->badge()
                                    ->color('success')
                                    ->columnSpan(1),

                                TextEntry::make('nights_qty')
                                    ->label('🌙 ' . __('app-quotation-itineraries.fields.nights'))
                                    ->badge()
                                    ->color('warning')
                                    ->columnSpan(1),

                                TextEntry::make('has_breakfast')
                                    ->label('🍳 ' . __('common-fields.breakfast'))
                                    ->formatStateUsing(fn($state) => $state ? __('common-fields.yes') : __('common-fields.no'))
                                    ->badge()
                                    ->color(fn($state) => $state ? 'success' : 'gray')
                                    ->columnSpan(1),

                                TextEntry::make('id')
                                    ->label('💰 ' . __('app-quotation-itineraries.fields.room_prices'))
                                    ->formatStateUsing(function ($state, $record, $livewire) {
                                        if (!$record->rooms || $record->rooms->isEmpty()) {
                                            return __('app-quotation-itineraries.breakdown_placeholders.no_rooms');
                                        }
                                        
                                        // Get currency from parent QuotationItinerary to avoid N+1
                                        $currency = $livewire->record->breakdown?->currency;
                                        $symbol = $currency?->symbol ?? $currency?->code ?? '';
                                        
                                        return nl2br(e(
                                            $record->rooms->map(function ($room) use ($symbol) {
                                                $roomName = $room->roomCategory?->name ?? 'Unknown';
                                                $price = number_format($room->price, 2);
                                                return "• {$roomName}: {$symbol}{$price}";
                                            })->implode("\n")
                                        ));
                                    })
                                    ->html()
                                    ->color('info')
                                    ->columnSpan(1),
                            ])
                    ])
            ])
            ->collapsible();
    }

    private static function attractionsSection(): Section
    {
        return Section::make(__('app-quotation-itineraries.sections.attractions.title'))
            ->description(__('app-quotation-itineraries.sections.attractions.description'))
            ->hidden(fn(QuotationItinerary $quotationItinerary) => !$quotationItinerary->breakdown)
            ->schema([
                RepeatableEntry::make('breakdown.attractions')
                    ->hiddenLabel()
                    ->contained(false)
                    ->schema([
                        Grid::make(5)
                            ->schema([
                                TextEntry::make('attraction.name')
                                    ->label('🏛️ ' . __('app-quotation-itineraries.fields.attraction'))
                                    ->weight('bold')
                                    ->color('primary')
                                    ->columnSpan(1),

                                TextEntry::make('city.name')
                                    ->label('📍 ' . __('common-fields.city'))
                                    ->badge()
                                    ->color('success')
                                    ->columnSpan(1),

                                TextEntry::make('is_outview')
                                    ->label('👁️ ' . __('app-quotation-itineraries.fields.outview'))
                                    ->formatStateUsing(fn($state) => $state ? __('common-fields.yes') : __('common-fields.no'))
                                    ->badge()
                                    ->color(fn($state) => $state ? 'warning' : 'success')
                                    ->columnSpan(1),

                                TextEntry::make('entry_price')
                                    ->label('💵 ' . __('app-quotation-itineraries.fields.entry_price'))
                                    ->formatStateUsing(function ($state, $record, $livewire) {
                                        // Get currency from parent QuotationItinerary to avoid N+1
                                        $currency = $livewire->record->breakdown?->currency;
                                        $symbol = $currency?->symbol ?? $currency?->code ?? '';
                                        return $symbol . number_format($state, 2);
                                    })
                                    ->badge()
                                    ->color('warning')
                                    ->columnSpan(1),

                                TextEntry::make('id')
                                    ->label('🎫 ' . __('app-quotation-itineraries.fields.sub_attractions'))
                                    ->formatStateUsing(function ($state, $record, $livewire) {
                                        if (!$record->subAttractions || $record->subAttractions->isEmpty()) {
                                            return __('app-quotation-itineraries.breakdown_placeholders.no_sub_attractions');
                                        }
                                        
                                        // Get currency from parent QuotationItinerary to avoid N+1
                                        $currency = $livewire->record->breakdown?->currency;
                                        $symbol = $currency?->symbol ?? $currency?->code ?? '';
                                        
                                        return nl2br(e(
                                            $record->subAttractions->map(function ($subAttraction) use ($symbol) {
                                                $name = $subAttraction->subAttraction?->name ?? 'Unknown';
                                                $price = number_format($subAttraction->price, 2);
                                                return "• {$name}: {$symbol}{$price}";
                                            })->implode("\n")
                                        ));
                                    })
                                    ->html()
                                    ->color('info')
                                    ->columnSpan(1),
                            ])
                    ])
            ])
            ->collapsible();
    }

    private static function companionsSection(): Section
    {
        return Section::make(__('app-quotation-itineraries.sections.companions.title'))
            ->description(__('app-quotation-itineraries.sections.companions.description'))
            ->hidden(fn(QuotationItinerary $quotationItinerary) => !$quotationItinerary->breakdown)
            ->schema([
                RepeatableEntry::make('breakdown.companions')
                    ->hiddenLabel()
                    ->contained(false)
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('companionType.name')
                                    ->label(__('app-quotation-itineraries.fields.companion_type'))
                                    ->icon('heroicon-o-user')
                                    ->color('primary'),

                                TextEntry::make('per_day_price')
                                    ->label(__('app-quotation-itineraries.fields.per_day_price'))
                                    ->money('CNY')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->color('success'),

                                TextEntry::make('half_day_price')
                                    ->label(__('app-quotation-itineraries.fields.half_day_price'))
                                    ->money('CNY')
                                    ->icon('heroicon-o-clock')
                                    ->color('warning'),

                                TextEntry::make('per_hour_price')
                                    ->label(__('app-quotation-itineraries.fields.per_hour_price'))
                                    ->money('CNY')
                                    ->icon('heroicon-o-clock')
                                    ->color('gray'),
                            ])
                    ])
            ])
            ->collapsible();
    }

    private static function expensesSection(): Section
    {
        return Section::make(__('app-quotation-itineraries.sections.additional_expenses.title'))
            ->description(__('app-quotation-itineraries.sections.additional_expenses.description'))
            ->hidden(fn(QuotationItinerary $quotationItinerary) => !$quotationItinerary->breakdown)
            ->schema([
                RepeatableEntry::make('breakdown.expenses')
                    ->hiddenLabel()
                    ->contained(false)
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('description')
                                    ->label(__('common-fields.description'))
                                    ->icon('heroicon-o-document-text')
                                    ->color('primary'),

                                TextEntry::make('price')
                                    ->label(__('app-quotation-itineraries.fields.price'))
                                    ->money('CNY')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->color('success'),

                                TextEntry::make('charge_mode')
                                    ->label(__('app-quotation-itineraries.fields.charge_mode'))
                                    ->formatStateUsing(fn($state) => $state?->label() ?? $state)
                                    ->badge()
                                    ->color('info'),
                            ])
                    ])
            ])
            ->collapsible();
    }

    private static function regenerateBreakdownAction(): Action
    {
        return Action::make('regenerate_breakdown')
            ->label(__('app-quotation-itineraries.actions.regenerate'))
            ->icon('heroicon-m-arrow-path')
            ->color(fn(QuotationItinerary $quotationItinerary) => 
                $quotationItinerary->itinerary?->is_complete ? 'primary' : 'gray'
            )
            ->disabled(fn(QuotationItinerary $quotationItinerary) => 
                !$quotationItinerary->itinerary?->is_complete
            )
            ->tooltip(fn(QuotationItinerary $quotationItinerary) => 
                !$quotationItinerary->itinerary?->is_complete 
                    ? __('app-quotation-itineraries.tooltips_breakdown.complete_itinerary_before_regenerate')
                    : null
            )
            ->action(function (QuotationItinerary $quotationItinerary) {
                if ($quotationItinerary->breakdown && $quotationItinerary->itinerary && $quotationItinerary->itinerary->is_complete) {
                    // Regenerate breakdown from itinerary
                    $quotationItinerary->generateBreakdownFromItinerary();

                    Notification::make()
                        ->title(__('app-quotation-itineraries.notifications.breakdown_regenerated_title'))
                        ->body(__('app-quotation-itineraries.notifications.breakdown_regenerated_body'))
                        ->success()
                        ->send();
                    
                    // Redirect to breakdown edit form
                    return redirect()->to(\App\Filament\App\Resources\QuotationItineraries\QuotationItineraryResource::getUrl('edit-breakdown', ['record' => $quotationItinerary]));
                } else {
                    Notification::make()
                        ->title(__('app-quotation-itineraries.notifications.cannot_regenerate_title'))
                        ->body(__('app-quotation-itineraries.notifications.cannot_regenerate_body'))
                        ->warning()
                        ->send();
                }
            })
            ->requiresConfirmation()
            ->modalHeading(__('app-quotation-itineraries.modals_breakdown.regenerate_breakdown_heading'))
            ->modalDescription(__('app-quotation-itineraries.modals_breakdown.regenerate_breakdown_description'))
            ->modalSubmitActionLabel(__('app-quotation-itineraries.actions.regenerate'));
    }

    private static function completeBreakdownAction(): Action
    {
        return Action::make('complete_breakdown')
            ->label(__('app-quotation-itineraries.actions.complete'))
            ->icon('heroicon-m-check-circle')
            ->color(fn(QuotationItinerary $quotationItinerary) => 
                $quotationItinerary->itinerary?->is_complete ? 'success' : 'gray'
            )
            ->hidden(function (QuotationItinerary $quotationItinerary) {
                return !$quotationItinerary->breakdown || $quotationItinerary->breakdown->is_completed;
            })
            ->disabled(fn(QuotationItinerary $quotationItinerary) => 
                !$quotationItinerary->itinerary?->is_complete
            )
            ->tooltip(fn(QuotationItinerary $quotationItinerary) => 
                !$quotationItinerary->itinerary?->is_complete 
                    ? __('app-quotation-itineraries.tooltips_breakdown.complete_itinerary_before_complete')
                    : null
            )
            ->action(function (QuotationItinerary $quotationItinerary) {
                // Check if itinerary is complete first
                if (!$quotationItinerary->itinerary || !$quotationItinerary->itinerary->is_complete) {
                    Notification::make()
                        ->title(__('app-quotation-itineraries.notifications.cannot_complete_breakdown_title'))
                        ->body(__('app-quotation-itineraries.notifications.cannot_complete_breakdown_body'))
                        ->warning()
                        ->send();
                    return;
                }

                if ($quotationItinerary->breakdown) {
                    $quotationItinerary->breakdown->update(['is_completed' => true]);
                    Notification::make()
                        ->title(__('app-quotation-itineraries.notifications.breakdown_completed_title'))
                        ->success()
                        ->send();
                }
            })
            ->requiresConfirmation()
            ->modalHeading(__('app-quotation-itineraries.modals_breakdown.complete_breakdown_heading'))
            ->modalDescription(function (QuotationItinerary $quotationItinerary) {
                if (!$quotationItinerary->itinerary || !$quotationItinerary->itinerary->is_complete) {
                    return __('app-quotation-itineraries.modals_breakdown.complete_breakdown_description_incomplete');
                }
                return __('app-quotation-itineraries.modals_breakdown.complete_breakdown_description');
            })
            ->modalSubmitActionLabel(__('app-quotation-itineraries.actions.complete'));
    }

    private static function editBreakdownAction(): Action
    {
        return Action::make('edit_breakdown')
            ->label(__('app-quotation-itineraries.actions.edit'))
            ->icon('heroicon-m-pencil-square')
            ->color(fn(QuotationItinerary $quotationItinerary) => 
                $quotationItinerary->itinerary?->is_complete ? 'primary' : 'gray'
            )
            ->disabled(fn(QuotationItinerary $quotationItinerary) => 
                !$quotationItinerary->itinerary?->is_complete
            )
            ->tooltip(fn(QuotationItinerary $quotationItinerary) => 
                !$quotationItinerary->itinerary?->is_complete 
                    ? __('app-quotation-itineraries.tooltips_breakdown.complete_itinerary_before_edit')
                    : null
            )
            ->url(fn(QuotationItinerary $quotationItinerary) => \App\Filament\App\Resources\QuotationItineraries\QuotationItineraryResource::getUrl('edit-breakdown', ['record' => $quotationItinerary]));
    }

    private static function deleteBreakdownAction(): Action
    {
        return Action::make('delete_breakdown')
            ->label(__('common-fields.delete'))
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading(__('app-quotation-itineraries.modals_breakdown.delete_breakdown_heading'))
            ->modalDescription(__('app-quotation-itineraries.modals_breakdown.delete_breakdown_description'))
            ->modalSubmitActionLabel(__('common-fields.delete'))
            ->action(function (QuotationItinerary $quotationItinerary) {
                if ($quotationItinerary->breakdown) {
                    $quotationItinerary->breakdown->delete();

                    // Refresh the record to update the UI
                    $quotationItinerary->refresh();

                    Notification::make()
                        ->title(__('app-quotation-itineraries.notifications.breakdown_deleted_title'))
                        ->success()
                        ->send();
                }
            });
    }
}
