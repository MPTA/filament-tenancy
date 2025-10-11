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
        return Tab::make('Breakdown')
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
        return Section::make('Create Breakdown')
            ->description('Start building your cost breakdown')
            ->hidden(fn(QuotationItinerary $quotationItinerary) => $quotationItinerary->breakdown)
            ->schema([
                Grid::make(1)
                    ->schema([
                        Action::make('Create Breakdown')
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
                                    ? 'Create Breakdown' 
                                    : 'Complete Itinerary First'
                            )
                            ->tooltip(fn(QuotationItinerary $quotationItinerary) => 
                                !$quotationItinerary->itinerary?->is_complete 
                                    ? 'Please complete the itinerary before creating breakdown' 
                                    : null
                            )
                            ->action(function (QuotationItinerary $quotationItinerary) {
                                // Check if itinerary is complete
                                if (!$quotationItinerary->itinerary || !$quotationItinerary->itinerary->is_complete) {
                                    Notification::make()
                                        ->title('Incomplete Itinerary')
                                        ->body('Please complete the itinerary first before generating breakdown.')
                                        ->warning()
                                        ->send();
                                    return;
                                }

                                // Generate breakdown from itinerary
                                $quotationItinerary->generateBreakdownFromItinerary();

                                // Refresh the record to update the UI
                                $quotationItinerary->refresh();

                                Notification::make()
                                    ->title('Breakdown Generated')
                                    ->body('Cost breakdown has been successfully generated. Redirecting to breakdown form...')
                                    ->success()
                                    ->send();

                                // Redirect to breakdown edit form
                                return redirect()->to(\App\Filament\App\Resources\QuotationItineraries\QuotationItineraryResource::getUrl('edit-breakdown', ['record' => $quotationItinerary]));
                            })
                            ->modalHeading('Create New Breakdown')
                            ->modalDescription('Create a detailed cost breakdown for this quotation itinerary')
                            ->modalSubmitActionLabel('Create Breakdown')
                    ])
                    ->extraAttributes(['class' => 'flex justify-center items-center min-h-[200px]'])
            ])
            ->collapsible(false);
    }

    private static function breakdownOverviewSection(): Section
    {
        return Section::make('Breakdown Overview')
            ->description('Cost breakdown summary and details')
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
                            ->label('Status')
                            ->formatStateUsing(fn($state) => $state ? 'Completed' : 'In Progress')
                            ->icon(fn($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-clock')
                            ->color(fn($state) => $state ? 'success' : 'warning')
                            ->columnStart(1),
                    ]),

                Grid::make(4)
                    ->schema([
                        TextEntry::make('breakdown.vehicle_days_qty')
                            ->label('Vehicle Days')
                            ->numeric()
                            ->icon('heroicon-o-truck')
                            ->color('primary'),

                        TextEntry::make('breakdown.vehicle_half_days_qty')
                            ->label('Half Days')
                            ->numeric()
                            ->icon('heroicon-o-clock')
                            ->color('warning'),

                        TextEntry::make('breakdown.vehicle_hours_qty')
                            ->label('Vehicle Hours')
                            ->numeric()
                            ->icon('heroicon-o-clock')
                            ->color('info'),

                        TextEntry::make('breakdown.currency.name')
                            ->label('Currency')
                            ->icon('heroicon-o-banknotes')
                            ->color('success'),
                    ]),

                Grid::make(2)
                    ->schema([
                        TextEntry::make('breakdown.driver_base_meal_budget')
                            ->label('Driver Meal Budget')
                            ->money('CNY')
                            ->icon('heroicon-o-currency-dollar')
                            ->color('success'),

                        TextEntry::make('breakdown.driver_base_accommodation_budget')
                            ->label('Driver Accommodation Budget')
                            ->money('CNY')
                            ->icon('heroicon-o-home')
                            ->color('primary'),

                        TextEntry::make('breakdown.companion_base_meal_budget')
                            ->label('Companion Meal Budget')
                            ->money('CNY')
                            ->icon('heroicon-o-currency-dollar')
                            ->color('warning'),

                        TextEntry::make('breakdown.companion_base_accommodation_budget')
                            ->label('Companion Accommodation Budget')
                            ->money('CNY')
                            ->icon('heroicon-o-home')
                            ->color('info'),
                    ]),
            ])
            ->collapsible(false);
    }

    private static function vehicleTypesSection(): Section
    {
        return Section::make('Vehicle Types')
            ->description('Vehicle pricing and details')
            ->hidden(fn(QuotationItinerary $quotationItinerary) => !$quotationItinerary->breakdown)
            ->schema([
                RepeatableEntry::make('breakdown.vehicleTypes')
                    ->hiddenLabel()
                    ->contained(false)
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('vehicleType.name')
                                    ->label('Vehicle Type')
                                    ->icon('heroicon-o-truck')
                                    ->color('primary'),

                                TextEntry::make('per_day_price')
                                    ->label('Per Day Price')
                                    ->money('CNY')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->color('success'),

                                TextEntry::make('half_day_price')
                                    ->label('Half Day Price')
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
        return Section::make('Tickets')
            ->description('Transportation tickets and pricing')
            ->hidden(fn(QuotationItinerary $quotationItinerary) => !$quotationItinerary->breakdown)
            ->schema([
                RepeatableEntry::make('breakdown.tickets')
                    ->hiddenLabel()
                    ->contained(false)
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('transport_mode')
                                    ->label('Transport Mode')
                                    ->badge()
                                    ->color('primary'),

                                TextEntry::make('fromCity.name')
                                    ->label('From City')
                                    ->icon('heroicon-o-map-pin')
                                    ->color('success'),

                                TextEntry::make('toCity.name')
                                    ->label('To City')
                                    ->icon('heroicon-o-map-pin')
                                    ->color('warning'),

                                TextEntry::make('class')
                                    ->label('Class')
                                    ->badge()
                                    ->color('info'),
                            ]),

                        TextEntry::make('price')
                            ->label('Price')
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
        return Section::make('Meals')
            ->description('Meal types and quantities')
            ->hidden(fn(QuotationItinerary $quotationItinerary) => !$quotationItinerary->breakdown)
            ->schema([
                RepeatableEntry::make('breakdown.meals')
                    ->hiddenLabel()
                    ->contained(false)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('mealType.name')
                                    ->label('Meal Type')
                                    ->icon('heroicon-o-cake')
                                    ->color('primary'),

                                TextEntry::make('price')
                                    ->label('Price')
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
        return Section::make('Experiences')
            ->description('Experience activities and pricing')
            ->hidden(fn(QuotationItinerary $quotationItinerary) => !$quotationItinerary->breakdown)
            ->schema([
                RepeatableEntry::make('breakdown.experiences')
                    ->hiddenLabel()
                    ->contained(false)
                    ->schema([
                        Grid::make(3)
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

                                TextEntry::make('charge_mode')
                                    ->label('Charge Mode')
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
        return Section::make('Accommodations')
            ->description('Hotel accommodations and room pricing')
            ->hidden(fn(QuotationItinerary $quotationItinerary) => !$quotationItinerary->breakdown)
            ->schema([
                RepeatableEntry::make('breakdown.accommodations')
                    ->hiddenLabel()
                    ->contained(false)
                    ->schema([
                        Grid::make(5)
                            ->schema([
                                TextEntry::make('accommodation.name')
                                    ->label('🏨 Hotel')
                                    ->weight('bold')
                                    ->color('primary')
                                    ->columnSpan(1),

                                TextEntry::make('city.name')
                                    ->label('📍 City')
                                    ->badge()
                                    ->color('success')
                                    ->columnSpan(1),

                                TextEntry::make('nights_qty')
                                    ->label('🌙 Nights')
                                    ->badge()
                                    ->color('warning')
                                    ->columnSpan(1),

                                TextEntry::make('has_breakfast')
                                    ->label('🍳 Breakfast')
                                    ->formatStateUsing(fn($state) => $state ? 'Yes' : 'No')
                                    ->badge()
                                    ->color(fn($state) => $state ? 'success' : 'gray')
                                    ->columnSpan(1),

                                TextEntry::make('id')
                                    ->label('💰 Room Prices')
                                    ->formatStateUsing(function ($state, $record, $livewire) {
                                        if (!$record->rooms || $record->rooms->isEmpty()) {
                                            return 'No rooms';
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
        return Section::make('Attractions')
            ->description('Tourist attractions and entry fees')
            ->hidden(fn(QuotationItinerary $quotationItinerary) => !$quotationItinerary->breakdown)
            ->schema([
                RepeatableEntry::make('breakdown.attractions')
                    ->hiddenLabel()
                    ->contained(false)
                    ->schema([
                        Grid::make(5)
                            ->schema([
                                TextEntry::make('attraction.name')
                                    ->label('🏛️ Attraction')
                                    ->weight('bold')
                                    ->color('primary')
                                    ->columnSpan(1),

                                TextEntry::make('city.name')
                                    ->label('📍 City')
                                    ->badge()
                                    ->color('success')
                                    ->columnSpan(1),

                                TextEntry::make('is_outview')
                                    ->label('👁️ Outview')
                                    ->formatStateUsing(fn($state) => $state ? 'Yes' : 'No')
                                    ->badge()
                                    ->color(fn($state) => $state ? 'warning' : 'success')
                                    ->columnSpan(1),

                                TextEntry::make('entry_price')
                                    ->label('💵 Entry Price')
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
                                    ->label('🎫 Sub-Attractions')
                                    ->formatStateUsing(function ($state, $record, $livewire) {
                                        if (!$record->subAttractions || $record->subAttractions->isEmpty()) {
                                            return 'No sub-attractions';
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
        return Section::make('Companions')
            ->description('Tour guides and companion services')
            ->hidden(fn(QuotationItinerary $quotationItinerary) => !$quotationItinerary->breakdown)
            ->schema([
                RepeatableEntry::make('breakdown.companions')
                    ->hiddenLabel()
                    ->contained(false)
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('companionType.name')
                                    ->label('Companion Type')
                                    ->icon('heroicon-o-user')
                                    ->color('primary'),

                                TextEntry::make('per_day_price')
                                    ->label('Per Day Price')
                                    ->money('CNY')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->color('success'),

                                TextEntry::make('half_day_price')
                                    ->label('Half Day Price')
                                    ->money('CNY')
                                    ->icon('heroicon-o-clock')
                                    ->color('warning'),

                                TextEntry::make('per_hour_price')
                                    ->label('Per Hour Price')
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
        return Section::make('Additional Expenses')
            ->description('Miscellaneous expenses and costs')
            ->hidden(fn(QuotationItinerary $quotationItinerary) => !$quotationItinerary->breakdown)
            ->schema([
                RepeatableEntry::make('breakdown.expenses')
                    ->hiddenLabel()
                    ->contained(false)
                    ->schema([
                        Grid::make(3)
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

                                TextEntry::make('charge_mode')
                                    ->label('Charge Mode')
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
            ->label('Regenerate')
            ->icon('heroicon-m-arrow-path')
            ->color(fn(QuotationItinerary $quotationItinerary) => 
                $quotationItinerary->itinerary?->is_complete ? 'primary' : 'gray'
            )
            ->disabled(fn(QuotationItinerary $quotationItinerary) => 
                !$quotationItinerary->itinerary?->is_complete
            )
            ->tooltip(fn(QuotationItinerary $quotationItinerary) => 
                !$quotationItinerary->itinerary?->is_complete 
                    ? 'Please complete the itinerary before regenerating breakdown' 
                    : null
            )
            ->action(function (QuotationItinerary $quotationItinerary) {
                if ($quotationItinerary->breakdown && $quotationItinerary->itinerary && $quotationItinerary->itinerary->is_complete) {
                    // Regenerate breakdown from itinerary
                    $quotationItinerary->generateBreakdownFromItinerary();

                    Notification::make()
                        ->title('Breakdown regenerated successfully!')
                        ->body('The breakdown has been updated. Redirecting to breakdown editor...')
                        ->success()
                        ->send();
                    
                    // Redirect to breakdown edit form
                    return redirect()->to(\App\Filament\App\Resources\QuotationItineraries\QuotationItineraryResource::getUrl('edit-breakdown', ['record' => $quotationItinerary]));
                } else {
                    Notification::make()
                        ->title('Cannot Regenerate')
                        ->body('Please complete the itinerary first before regenerating breakdown.')
                        ->warning()
                        ->send();
                }
            })
            ->requiresConfirmation()
            ->modalHeading('Regenerate Breakdown')
            ->modalDescription('This will update the breakdown based on the current itinerary. Are you sure?')
            ->modalSubmitActionLabel('Regenerate');
    }

    private static function completeBreakdownAction(): Action
    {
        return Action::make('complete_breakdown')
            ->label('Complete')
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
                    ? 'Please complete the itinerary before marking breakdown as complete' 
                    : null
            )
            ->action(function (QuotationItinerary $quotationItinerary) {
                // Check if itinerary is complete first
                if (!$quotationItinerary->itinerary || !$quotationItinerary->itinerary->is_complete) {
                    Notification::make()
                        ->title('Cannot Complete Breakdown')
                        ->body('Please complete the itinerary first before marking the breakdown as complete.')
                        ->warning()
                        ->send();
                    return;
                }

                if ($quotationItinerary->breakdown) {
                    $quotationItinerary->breakdown->update(['is_completed' => true]);
                    Notification::make()
                        ->title('Breakdown completed successfully!')
                        ->success()
                        ->send();
                }
            })
            ->requiresConfirmation()
            ->modalHeading('Complete Breakdown')
            ->modalDescription(function (QuotationItinerary $quotationItinerary) {
                if (!$quotationItinerary->itinerary || !$quotationItinerary->itinerary->is_complete) {
                    return 'Please complete the itinerary first before marking the breakdown as complete.';
                }
                return 'Are you sure you want to mark this breakdown as complete?';
            })
            ->modalSubmitActionLabel('Complete');
    }

    private static function editBreakdownAction(): Action
    {
        return Action::make('edit_breakdown')
            ->label('Edit')
            ->icon('heroicon-m-pencil-square')
            ->color(fn(QuotationItinerary $quotationItinerary) => 
                $quotationItinerary->itinerary?->is_complete ? 'primary' : 'gray'
            )
            ->disabled(fn(QuotationItinerary $quotationItinerary) => 
                !$quotationItinerary->itinerary?->is_complete
            )
            ->tooltip(fn(QuotationItinerary $quotationItinerary) => 
                !$quotationItinerary->itinerary?->is_complete 
                    ? 'Please complete the itinerary before editing breakdown' 
                    : null
            )
            ->url(fn(QuotationItinerary $quotationItinerary) => \App\Filament\App\Resources\QuotationItineraries\QuotationItineraryResource::getUrl('edit-breakdown', ['record' => $quotationItinerary]));
    }

    private static function deleteBreakdownAction(): Action
    {
        return Action::make('delete_breakdown')
            ->label('Delete')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Delete Breakdown')
            ->modalDescription('Are you sure you want to delete this breakdown? This action cannot be undone.')
            ->modalSubmitActionLabel('Delete')
            ->action(function (QuotationItinerary $quotationItinerary) {
                if ($quotationItinerary->breakdown) {
                    $quotationItinerary->breakdown->delete();

                    // Refresh the record to update the UI
                    $quotationItinerary->refresh();

                    Notification::make()
                        ->title('Breakdown deleted successfully!')
                        ->success()
                        ->send();
                }
            });
    }
}
