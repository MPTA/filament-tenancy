<?php

namespace App\Filament\App\Resources\QuotationItineraries\Schemas;

use App\Enums\TransportModeEnum;
use App\Enums\TravelModeEnum;
use App\Filament\App\Resources\Itineraries\ItineraryResource;
use App\Filament\Shared\Schemas\TransportationRepeater;
use App\Models\Tenants\QuotationItinerary;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\Size;
use Illuminate\Support\Facades\Auth;

class ItineraryTab
{
    public static function getTab(): Tab
    {
        return Tab::make('Itinerary')
            ->icon('heroicon-o-map')
            ->badge(function (QuotationItinerary $record) {
                if (!$record->itinerary) {
                    return null; // No badge when no itinerary
                }

                if ($record->itinerary->is_complete) {
                    return '✓'; // Green tick when complete
                }

                return '⏳'; // Pending symbol when incomplete
            })
            ->badgeColor(function (QuotationItinerary $record) {
                if (!$record->itinerary) {
                    return 'gray';
                }

                if ($record->itinerary->is_complete) {
                    return 'success';
                }

                return 'gray'; // Gray for pending
            })
            ->schema([
                self::itinerarySummarySection(),
                self::transportationsSection(),
                self::createItinerarySection(),
                self::itineraryDaysSection(),
            ]);
    }

    private static function transportationsSection(): Section
    {
        return Section::make('Group Transportations')
            ->description('Entry and exit transportation details for the group')
            ->icon('heroicon-o-paper-airplane')
            ->collapsible()
            ->collapsed(false)
            ->hidden(fn(QuotationItinerary $record) => !$record->itinerary)
            ->headerActions([
                // Create Transportation Action (shown when no transportations exist)
                Action::make('Create Transportation')
                    ->icon('heroicon-m-plus-circle')
                    ->color('success')
                    ->hidden(fn(QuotationItinerary $record) => $record->transportations->isNotEmpty())
                    ->fillForm(function (QuotationItinerary $quotationItinerary) {
                        $inquiryItinerary = $quotationItinerary->quotation->inquiry->inquiryItinerary;
                        
                        // Pre-fill transportation dates if inquiry has Fixed Date type and dates
                        if ($inquiryItinerary && 
                            $inquiryItinerary->date_type === \App\Enums\InquiryDateTypeEnum::FIXED_DATE &&
                            $inquiryItinerary->from_date && 
                            $inquiryItinerary->to_date) {
                            
                            return [
                                'transportations' => [
                                    [
                                        // Entry: arrival_date = from_date (when arriving to the country)
                                        'arrival_date' => $inquiryItinerary->from_date->format('Y-m-d'),
                                    ],
                                    [
                                        // Exit: departure_date = to_date (when leaving the country)
                                        'departure_date' => $inquiryItinerary->to_date->format('Y-m-d'),
                                    ],
                                ],
                            ];
                        }
                        
                        return [];
                    })
                    ->form([
                        TransportationRepeater::make(false)
                    ])
                    ->action(function (array $data, QuotationItinerary $quotationItinerary) {
                        // Create transportations
                        if (!empty($data['transportations'])) {
                            foreach ($data['transportations'] as $transportationData) {
                                // Skip empty transportations
                                if (!empty($transportationData['transport_mode']) || !empty($transportationData['from_city_id'])) {
                                    $quotationItinerary->transportations()->create($transportationData);
                                }
                            }
                            
                            // Update InquiryItinerary dates
                            // from_date = Entry arrival_date (first item - when arriving)
                            // to_date = Exit departure_date (second item - when leaving)
                            $fromDate = $data['transportations'][0]['arrival_date'] ?? null;
                            $toDate = $data['transportations'][1]['departure_date'] ?? null;
                            
                            if ($fromDate || $toDate) {
                                $quotationItinerary->quotation->inquiry->inquiryItinerary->update(array_filter([
                                    'from_date' => $fromDate,
                                    'to_date' => $toDate,
                                ]));
                            }
                        }
                        
                        Notification::make()
                            ->title('Transportation created successfully!')
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Create Group Transportation')
                    ->modalDescription('Enter entry and exit transportation details')
                    ->modalSubmitActionLabel('Create')
                    ->modalWidth('3xl'),
                
                // Edit Transportation Action (shown when transportations exist)
                Action::make('Edit Transportation')
                    ->icon('heroicon-m-pencil-square')
                    ->color('primary')
                    ->hidden(fn(QuotationItinerary $record) => $record->transportations->isEmpty())
                    ->fillForm(fn (QuotationItinerary $record) => [
                        'transportations' => $record->transportations->map(function ($transportation) {
                            return [
                                'id' => $transportation->id,
                                'transport_mode' => $transportation->transport_mode?->value,
                                'from_city_id' => $transportation->from_city_id,
                                'to_city_id' => $transportation->to_city_id,
                                'departure_date' => $transportation->departure_date?->format('Y-m-d'),
                                'departure_time' => $transportation->departure_time?->format('H:i'),
                                'arrival_date' => $transportation->arrival_date?->format('Y-m-d'),
                                'arrival_time' => $transportation->arrival_time?->format('H:i'),
                                'transport_number' => $transportation->transport_number,
                                'departure_airport_terminal' => $transportation->departure_airport_terminal,
                                'arrival_airport_terminal' => $transportation->arrival_airport_terminal,
                                'entry_border_id' => $transportation->entry_border_id,
                                'exit_border_id' => $transportation->exit_border_id,
                            ];
                        })->toArray()
                    ])
                    ->form([
                        TransportationRepeater::make(false)
                    ])
                    ->action(function (array $data, QuotationItinerary $quotationItinerary) {
                        // Delete existing transportations
                        $quotationItinerary->transportations()->delete();
                        
                        // Create new transportations
                        if (!empty($data['transportations'])) {
                            foreach ($data['transportations'] as $transportationData) {
                                // Skip empty transportations
                                if (!empty($transportationData['transport_mode']) || !empty($transportationData['from_city_id'])) {
                                    $quotationItinerary->transportations()->create($transportationData);
                                }
                            }
                            
                            // Update InquiryItinerary dates
                            // from_date = Entry arrival_date (first item - when arriving)
                            // to_date = Exit departure_date (second item - when leaving)
                            $fromDate = $data['transportations'][0]['arrival_date'] ?? null;
                            $toDate = $data['transportations'][1]['departure_date'] ?? null;
                            
                            if ($fromDate || $toDate) {
                                $quotationItinerary->quotation->inquiry->inquiryItinerary->update(array_filter([
                                    'from_date' => $fromDate,
                                    'to_date' => $toDate,
                                ]));
                            }
                        }
                        
                        Notification::make()
                            ->title('Transportation updated successfully!')
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Edit Group Transportation')
                    ->modalDescription('Update entry and exit transportation details')
                    ->modalSubmitActionLabel('Save')
                    ->modalWidth('3xl')
            ])
            ->schema([
                // Compact table-like display using Grid
                Grid::make(['default' => 6])
                    ->schema([
                        // Header row
                        TextEntry::make('header_type')
                            ->hiddenLabel()
                            ->formatStateUsing(fn() => 'Type')
                            ->weight('bold')
                            ->color('gray'),
                        
                        TextEntry::make('header_from')
                            ->hiddenLabel()
                            ->formatStateUsing(fn() => 'From')
                            ->weight('bold')
                            ->color('gray'),
                        
                        TextEntry::make('header_to')
                            ->hiddenLabel()
                            ->formatStateUsing(fn() => 'To')
                            ->weight('bold')
                            ->color('gray'),
                        
                        TextEntry::make('header_departure')
                            ->hiddenLabel()
                            ->formatStateUsing(fn() => 'Departure')
                            ->weight('bold')
                            ->color('gray'),
                        
                        TextEntry::make('header_arrival')
                            ->hiddenLabel()
                            ->formatStateUsing(fn() => 'Arrival')
                            ->weight('bold')
                            ->color('gray'),
                        
                        TextEntry::make('header_number')
                            ->hiddenLabel()
                            ->formatStateUsing(fn() => 'Number')
                            ->weight('bold')
                            ->color('gray'),
                    ])
                    ->extraAttributes(['class' => 'border-b pb-2 mb-2'])
                    ->hidden(fn(QuotationItinerary $record) => $record->transportations->isEmpty()),
                
                // Entry Transportation (index 0)
                Grid::make(['default' => 6])
                    ->schema([
                        TextEntry::make('transportations.0.transport_mode')
                            ->hiddenLabel()
                            ->badge()
                            ->formatStateUsing(fn($state) => $state?->label() ?? '-')
                            ->color(fn($state) => match($state) {
                                TransportModeEnum::AIR => 'primary',
                                TransportModeEnum::TRAIN => 'success',
                                TransportModeEnum::LAND => 'warning',
                                default => 'gray'
                            }),

                        TextEntry::make('transportations.0.fromCity.name')
                            ->hiddenLabel()
                            ->default('-'),

                        TextEntry::make('transportations.0.toCity.name')
                            ->hiddenLabel()
                            ->default('-'),

                        TextEntry::make('transportations.0.formatted_departure_datetime')
                            ->hiddenLabel()
                            ->default('-'),

                        TextEntry::make('transportations.0.formatted_arrival_datetime')
                            ->hiddenLabel()
                            ->default('-'),

                        TextEntry::make('transportations.0.transport_number')
                            ->hiddenLabel()
                            ->default('-'),
                    ])
                    ->extraAttributes(['class' => 'mb-2'])
                    ->hidden(fn(QuotationItinerary $record) => !isset($record->transportations[0])),
                
                // Exit Transportation (index 1)
                Grid::make(['default' => 6])
                    ->schema([
                        TextEntry::make('transportations.1.transport_mode')
                            ->hiddenLabel()
                            ->badge()
                            ->formatStateUsing(fn($state) => $state?->label() ?? '-')
                            ->color(fn($state) => match($state) {
                                TransportModeEnum::AIR => 'primary',
                                TransportModeEnum::TRAIN => 'success',
                                TransportModeEnum::LAND => 'warning',
                                default => 'gray'
                            }),

                        TextEntry::make('transportations.1.fromCity.name')
                            ->hiddenLabel()
                            ->default('-'),

                        TextEntry::make('transportations.1.toCity.name')
                            ->hiddenLabel()
                            ->default('-'),

                        TextEntry::make('transportations.1.formatted_departure_datetime')
                            ->hiddenLabel()
                            ->default('-'),

                        TextEntry::make('transportations.1.formatted_arrival_datetime')
                            ->hiddenLabel()
                            ->default('-'),

                        TextEntry::make('transportations.1.transport_number')
                            ->hiddenLabel()
                            ->default('-'),
                    ])
                    ->hidden(fn(QuotationItinerary $record) => !isset($record->transportations[1])),
                
                // Placeholder when no transportations exist
                \Filament\Infolists\Components\TextEntry::make('no_transportations')
                    ->hiddenLabel()
                    ->formatStateUsing(fn () => 'No transportation details available. Click "Create Transportation" to add entry and exit transportation.')
                    ->color('gray')
                    ->hidden(fn(QuotationItinerary $record) => $record->transportations->isNotEmpty())
            ]);
    }

    private static function itinerarySummarySection(): Section
    {
        return Section::make('Itinerary Summary')
            ->description('Quick overview of your travel plan')
            ->hidden(fn(QuotationItinerary $quotationItinerary) => !$quotationItinerary->itinerary)
            ->headerActions([
                self::completeItineraryAction(),
            ])
            ->schema([
                Grid::make(4)
                    ->schema([
                        TextEntry::make('itinerary.travel_mode')
                            ->label('Travel Mode')
                            ->formatStateUsing(fn($state) => $state?->getLabel() ?? 'Not specified')
                            ->icon('heroicon-o-globe-alt')
                            ->color('primary'),

                        TextEntry::make('itinerary.id')
                            ->label('Total Days')
                            ->formatStateUsing(fn($state, $record) => $record->itinerary?->days?->count() ?? 0)
                            ->icon('heroicon-o-calendar')
                            ->color('success'),

                        TextEntry::make('itinerary.id')
                            ->label('Total Activities')
                            ->formatStateUsing(fn($state, $record) => $record->itinerary?->days?->sum(fn($day) => $day->activities?->count() ?? 0) ?? 0)
                            ->icon('heroicon-o-map-pin')
                            ->color('warning'),

                        TextEntry::make('itinerary.is_complete')
                            ->label('Status')
                            ->formatStateUsing(fn($state) => $state ? 'Completed' : 'In Progress')
                            ->icon(fn($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-clock')
                            ->color(fn($state) => $state ? 'success' : 'warning')
                    ])
            ])
            ->collapsible(false);
    }

    private static function createItinerarySection(): Section
    {
        return Section::make('Create Itinerary')
            ->description('Start building your travel plan')
            ->hidden(fn(QuotationItinerary $quotationItinerary) => $quotationItinerary->itinerary)
            ->schema([
                Grid::make(1)
                    ->schema([
                        Action::make('Create Itinerary')
                            ->size(Size::ExtraLarge)
                            ->icon('heroicon-m-plus-circle')
                            ->color('success')
                            ->fillForm(function (QuotationItinerary $quotationItinerary) {
                                // Don't pre-fill - let toggle control it
                                return [];
                            })
                            ->schema([
                                Select::make('travel_mode')
                                    ->label('Travel Mode')
                                    ->options(TravelModeEnum::getOptions())
                                    ->required()
                                    ->reactive()
                                    ->placeholder('Select travel mode'),
                                
                                Toggle::make('enter_transportation_details')
                                    ->label('Enter Transportation Details')
                                    ->helperText('Enable to add entry and exit transportation information')
                                    ->reactive()
                                    ->default(false)
                                    ->afterStateUpdated(function ($state, $set, $get, $livewire) {
                                        // When toggle is enabled, create transportations with auto-filled data
                                        if ($state) {
                                            $quotationItinerary = $livewire->record;
                                            $inquiryItinerary = $quotationItinerary->quotation->inquiry->inquiryItinerary;
                                            $travelMode = $get('travel_mode');
                                            
                                            // Prepare transport_mode based on travel_mode
                                            $transportMode = null;
                                            if ($travelMode) {
                                                $transportMode = match($travelMode) {
                                                    'air' => TransportModeEnum::AIR->value,
                                                    'land' => TransportModeEnum::LAND->value,
                                                    default => null
                                                };
                                            }
                                            
                                            // Build transportation items
                                            $transportations = [
                                                [
                                                    'transport_mode' => $transportMode,
                                                    'arrival_date' => ($inquiryItinerary && 
                                                                      $inquiryItinerary->date_type === \App\Enums\InquiryDateTypeEnum::FIXED_DATE && 
                                                                      $inquiryItinerary->from_date) 
                                                                      ? $inquiryItinerary->from_date->format('Y-m-d') 
                                                                      : null,
                                                ],
                                                [
                                                    'transport_mode' => $transportMode,
                                                    'departure_date' => ($inquiryItinerary && 
                                                                        $inquiryItinerary->date_type === \App\Enums\InquiryDateTypeEnum::FIXED_DATE && 
                                                                        $inquiryItinerary->to_date) 
                                                                        ? $inquiryItinerary->to_date->format('Y-m-d') 
                                                                        : null,
                                                ],
                                            ];
                                            
                                            $set('transportations', $transportations);
                                        }
                                    }),
                                
                                TransportationRepeater::make(false)
                                    ->columnSpanFull()
                                    ->visible(fn ($get) => $get('enter_transportation_details') === true)
                            ])
                            ->action(function (array $data, QuotationItinerary $quotationItinerary) {
                                if (!$quotationItinerary->itinerary) {
                                    // 1. Save transportations if provided and toggle is enabled
                                    if (!empty($data['enter_transportation_details']) && !empty($data['transportations'])) {
                                        foreach ($data['transportations'] as $transportationData) {
                                            // Skip empty transportations
                                            if (!empty($transportationData['transport_mode']) || !empty($transportationData['from_city_id'])) {
                                                $quotationItinerary->transportations()->create($transportationData);
                                            }
                                        }
                                        
                                        // 2. Update InquiryItinerary dates from transportations
                                        // from_date = Entry arrival_date (first item - when arriving)
                                        // to_date = Exit departure_date (second item - when leaving)
                                        $fromDate = $data['transportations'][0]['arrival_date'] ?? null;
                                        $toDate = $data['transportations'][1]['departure_date'] ?? null;
                                        
                                        if ($fromDate || $toDate) {
                                            $quotationItinerary->quotation->inquiry->inquiryItinerary->update(array_filter([
                                                'from_date' => $fromDate,
                                                'to_date' => $toDate,
                                            ]));
                                        }
                                    }
                                    
                                    // 3. Create Itinerary
                                    $itinerary = $quotationItinerary->itinerary()->create([
                                        'travel_mode' => $data['travel_mode'],
                                        'creator_user_id' => Auth::user()->id,
                                    ]);
                                    
                                    // Redirect to edit itinerary page
                                    return redirect(ItineraryResource::getUrl('edit', ['record' => $itinerary->id]));
                                }
                                $quotationItinerary->refresh();
                            })
                            ->modalHeading('Create New Itinerary')
                            ->modalDescription('Enter transportation details and choose travel mode')
                            ->modalSubmitActionLabel('Create Itinerary')
                            ->modalWidth('3xl')
                    ])
                    ->extraAttributes(['class' => 'flex justify-center items-center min-h-[200px]'])
            ])
            ->collapsible(false);
    }

    private static function itineraryDaysSection(): Section
    {
        return Section::make('Itinerary Days')
            ->description('Your travel plan day by day')
            ->compact()
            ->headerActions([
                self::editItineraryAction(),
            ])
            ->hidden(function (QuotationItinerary $quotationItinerary) {
                // Hide if no itinerary or no days
                if (!$quotationItinerary->itinerary) {
                    return true;
                }

                // Use loaded relationship instead of query
                if ($quotationItinerary->itinerary->relationLoaded('days') && $quotationItinerary->itinerary->days->count() === 0) {
                    return true;
                } elseif (!$quotationItinerary->itinerary->relationLoaded('days') && $quotationItinerary->itinerary->days()->count() === 0) {
                    return true;
                }

                return false;
            })
            ->schema([
                RepeatableEntry::make('itinerary.days')
                    ->hiddenLabel()
                    ->contained(false)
                    ->label('')
                    ->schema([
                        // Day Header
                        Section::make()
                            ->heading(function ($record) {
                                $heading = "Day {$record->day_number}";
                                
                                $badges = [];
                                // Check for vehicle
                                if ($record->vehicle_usage_mode || $record->vehicle_type_id) {
                                    $badges[] = '🚗';
                                }
                                
                                // Check for companion
                                if ($record->companion_hire_mode?->value === 'daily') {
                                    $badges[] = '👤';
                                }
                                
                                if (!empty($badges)) {
                                    $heading .= ' ' . implode(' ', $badges);
                                }
                                
                                return $heading;
                            })
                            ->description(function ($record) {
                                $items = [];
                                $cityName = $record->accommodationCity?->name ?? $record->currentCity?->name ?? 'Unknown City';
                                $items[] = '📍 ' . $cityName;

                                // Add hotel name and star rating
                                if ($record->accommodation) {
                                    $hotelName = $record->accommodation?->getTranslation('name', app()->getLocale()) ?? 'Not specified';
                                    $items[] = '🏨 ' . $hotelName;

                                    if ($record->accommodation_star_rating) {
                                        $stars = str_repeat('★', $record->accommodation_star_rating->value);
                                        $items[] = $stars;
                                    }
                                }

                                // Add BLD (Breakfast, Lunch, Dinner) status
                                $bldItems = [];

                                // Check for Breakfast using helper method
                                $breakfastActivity = self::getMealActivity($record, \App\Enums\MealPartEnum::BREAKFAST);
                                if ($breakfastActivity?->meal?->mealType?->name) {
                                    $bldItems[] = 'B';
                                }

                                // Check for Lunch using helper method
                                $lunchActivity = self::getMealActivity($record, \App\Enums\MealPartEnum::LUNCH);
                                if ($lunchActivity?->meal?->mealType?->name) {
                                    $bldItems[] = 'L';
                                }

                                // Check for Dinner using helper method
                                $dinnerActivity = self::getMealActivity($record, \App\Enums\MealPartEnum::DINNER);
                                if ($dinnerActivity?->meal?->mealType?->name) {
                                    $bldItems[] = 'D';
                                }

                                if (!empty($bldItems)) {
                                    $items[] = '🍽️ ' . implode('', $bldItems);
                                }
                                
                                // Add vehicle info
                                if ($record->vehicle_usage_mode || $record->vehicle_type_id) {
                                    $vehicleInfo = '🚗 Vehicle';
                                    if ($record->vehicleType) {
                                        $vehicleInfo .= ': ' . $record->vehicleType->name;
                                    }
                                    $items[] = $vehicleInfo;
                                }
                                
                                // Add companion info
                                if ($record->companion_hire_mode?->value === 'daily') {
                                    $companionInfo = '👤 Companion';
                                    if ($record->companionCategory) {
                                        $companionInfo .= ': ' . $record->companionCategory->name;
                                    }
                                    $items[] = $companionInfo;
                                }

                                return implode(' | ', $items);
                            })
                            ->icon('heroicon-o-calendar')
                            ->collapsible()
                            ->collapsed()
                            ->schema([
                                self::mealsSection(),
                                self::vehicleSection(),
                                self::companionSection(),
                                self::ticketsSection(),
                                self::attractionsSection(),
                                self::experiencesSection(),
                                self::descriptionSection(),
                            ])
                    ])
                    ->columns(1)
            ]);
    }

    /**
     * Helper method to get meal activity by part using loaded relationships
     */
    private static function getMealActivity($record, $mealPart)
    {
        if ($record->relationLoaded('activities')) {
            return $record->activities->first(function ($activity) use ($mealPart) {
                return $activity->relationLoaded('activityCategory') 
                    && $activity->activityCategory?->type === \App\Enums\ActivityCategoryTypeEnum::MEAL
                    && $activity->relationLoaded('meal')
                    && $activity->meal?->meal_part === $mealPart;
            });
        }

        // Fallback to query if not loaded
        return $record->activities()
            ->whereHas('activityCategory', function ($query) {
                $query->where('type', \App\Enums\ActivityCategoryTypeEnum::MEAL->value);
            })
            ->whereHas('meal', function ($query) use ($mealPart) {
                $query->where('meal_part', $mealPart->value);
            })
            ->with('meal.mealType')
            ->first();
    }

    private static function mealsSection(): Grid
    {
        return Grid::make(3)
            ->schema([
                TextEntry::make('id')
                    ->label('🌅 Breakfast')
                    ->formatStateUsing(function ($state, $record) {
                        $mealActivity = self::getMealActivity($record, \App\Enums\MealPartEnum::BREAKFAST);
                        return $mealActivity?->meal?->mealType?->name ?? null;
                    })
                    ->color('warning')
                    ->hidden(function ($state, $record) {
                        $mealActivity = self::getMealActivity($record, \App\Enums\MealPartEnum::BREAKFAST);
                        return empty($mealActivity?->meal?->mealType?->name);
                    }),

                TextEntry::make('id')
                    ->label('☀️ Lunch')
                    ->formatStateUsing(function ($state, $record) {
                        $mealActivity = self::getMealActivity($record, \App\Enums\MealPartEnum::LUNCH);
                        return $mealActivity?->meal?->mealType?->name ?? null;
                    })
                    ->color('success')
                    ->hidden(function ($state, $record) {
                        $mealActivity = self::getMealActivity($record, \App\Enums\MealPartEnum::LUNCH);
                        return empty($mealActivity?->meal?->mealType?->name);
                    }),

                TextEntry::make('id')
                    ->label('🌙 Dinner')
                    ->formatStateUsing(function ($state, $record) {
                        $mealActivity = self::getMealActivity($record, \App\Enums\MealPartEnum::DINNER);
                        return $mealActivity?->meal?->mealType?->name ?? null;
                    })
                    ->color('info')
                    ->hidden(function ($state, $record) {
                        $mealActivity = self::getMealActivity($record, \App\Enums\MealPartEnum::DINNER);
                        return empty($mealActivity?->meal?->mealType?->name);
                    }),
            ])
            ->columnSpanFull();
    }

    private static function ticketsSection(): TextEntry
    {
        return TextEntry::make('id')
            ->label('🎫 Tickets')
            ->formatStateUsing(function ($state, $record) {
                // Use loaded activities if available
                if ($record->relationLoaded('activities')) {
                    $ticketActivities = $record->activities->filter(function ($activity) {
                        return $activity->relationLoaded('activityCategory') 
                            && $activity->activityCategory?->type === \App\Enums\ActivityCategoryTypeEnum::TICKET;
                    });
                } else {
                    $ticketActivities = $record->activities()
                        ->whereHas('activityCategory', function ($query) {
                            $query->where('type', \App\Enums\ActivityCategoryTypeEnum::TICKET->value);
                        })
                        ->with('ticket.toCity')
                        ->get();
                }

                if ($ticketActivities->isEmpty()) {
                    return null;
                }

                $ticketInfo = [];
                foreach ($ticketActivities as $activity) {
                    if ($activity->ticket) {
                        $fromCity = $activity->city?->name ?? 'Unknown';
                        $toCity = $activity->ticket->toCity?->name ?? 'Unknown';
                        $class = $activity->ticket->class?->value ?? 'Unknown';
                        $transportNumber = $activity->ticket->transport_number ?? 'N/A';
                        $departureTime = $activity->start_time?->format('H:i') ?? 'N/A';
                        $transportMode = $activity->ticket->transport_mode ?? null;

                        $icon = match ($transportMode) {
                            \App\Enums\TransportModeEnum::AIR->value => '✈️',
                            \App\Enums\TransportModeEnum::TRAIN->value => '🚂',
                            \App\Enums\TransportModeEnum::LAND->value => '🚗',
                            default => '🎫'
                        };

                        $ticketInfo[] = "{$icon} {$fromCity} → {$toCity} ({$class}) - {$transportNumber} at {$departureTime}";
                    }
                }

                return implode(' | ', $ticketInfo);
            })
            ->icon('heroicon-o-ticket')
            ->color('primary')
            ->hidden(function ($state, $record) {
                // Use loaded activities if available
                if ($record->relationLoaded('activities')) {
                    $ticketActivities = $record->activities->filter(function ($activity) {
                        return $activity->relationLoaded('activityCategory') 
                            && $activity->activityCategory?->type === \App\Enums\ActivityCategoryTypeEnum::TICKET;
                    });
                } else {
                    $ticketActivities = $record->activities()
                        ->whereHas('activityCategory', function ($query) {
                            $query->where('type', \App\Enums\ActivityCategoryTypeEnum::TICKET->value);
                        })
                        ->get();
                }
                return $ticketActivities->isEmpty();
            })
            ->columnSpanFull();
    }

    private static function attractionsSection(): TextEntry
    {
        return TextEntry::make('id')
            ->label('🏛️ Attractions')
            ->formatStateUsing(function ($state, $record) {
                // Use loaded activities if available
                if ($record->relationLoaded('activities')) {
                    $attractionActivities = $record->activities->filter(function ($activity) {
                        return $activity->relationLoaded('activityCategory') 
                            && $activity->activityCategory?->type === \App\Enums\ActivityCategoryTypeEnum::ATTRACTION;
                    });
                } else {
                    $attractionActivities = $record->activities()
                        ->whereHas('activityCategory', function ($query) {
                            $query->where('type', \App\Enums\ActivityCategoryTypeEnum::ATTRACTION->value);
                        })
                        ->with(['attraction.attraction', 'attraction.subAttractions.subAttraction'])
                        ->get();
                }

                if ($attractionActivities->isEmpty()) {
                    return null;
                }

                $attractionInfo = [];
                foreach ($attractionActivities as $activity) {
                    if ($activity->attraction) {
                        $attractionName = $activity->attraction->attraction?->name ?? 'Unknown';
                        $isOutview = $activity->attraction->is_outview ? ' (Outview)' : '';

                        $subAttractions = $activity->attraction->subAttractions
                            ->map(fn($sub) => $sub->subAttraction?->name ?? 'Unknown')
                            ->filter()
                            ->values();

                        $subInfo = '';
                        if ($subAttractions->isNotEmpty()) {
                            $subList = $subAttractions->implode(', ');
                            $subInfo = " ({$subList})";
                        }

                        $attractionInfo[] = "🏛️ {$attractionName}{$isOutview}{$subInfo}";
                    }
                }

                return implode(' | ', $attractionInfo);
            })
            ->icon('heroicon-o-building-library')
            ->color('info')
            ->hidden(function ($state, $record) {
                // Use loaded activities if available
                if ($record->relationLoaded('activities')) {
                    $attractionActivities = $record->activities->filter(function ($activity) {
                        return $activity->relationLoaded('activityCategory') 
                            && $activity->activityCategory?->type === \App\Enums\ActivityCategoryTypeEnum::ATTRACTION;
                    });
                } else {
                    $attractionActivities = $record->activities()
                        ->whereHas('activityCategory', function ($query) {
                            $query->where('type', \App\Enums\ActivityCategoryTypeEnum::ATTRACTION->value);
                        })
                        ->get();
                }
                return $attractionActivities->isEmpty();
            })
            ->columnSpanFull();
    }

    private static function experiencesSection(): TextEntry
    {
        return TextEntry::make('id')
            ->label('🎭 Experiences')
            ->formatStateUsing(function ($state, $record) {
                // Use loaded activities if available
                if ($record->relationLoaded('activities')) {
                    $experienceActivities = $record->activities->filter(function ($activity) {
                        return $activity->relationLoaded('activityCategory') 
                            && $activity->activityCategory?->type === \App\Enums\ActivityCategoryTypeEnum::EXPERIENCE;
                    });
                } else {
                    $experienceActivities = $record->activities()
                        ->whereHas('activityCategory', function ($query) {
                            $query->where('type', \App\Enums\ActivityCategoryTypeEnum::EXPERIENCE->value);
                        })
                        ->with('experience.experience')
                        ->get();
                }

                if ($experienceActivities->isEmpty()) {
                    return null;
                }

                $experienceInfo = [];
                foreach ($experienceActivities as $activity) {
                    if ($activity->experience) {
                        $experienceName = $activity->experience->experience?->name ?? 'Unknown';
                        $experienceInfo[] = "🎭 {$experienceName}";
                    }
                }

                return implode(' | ', $experienceInfo);
            })
            ->icon('heroicon-o-sparkles')
            ->color('warning')
            ->hidden(function ($state, $record) {
                // Use loaded activities if available
                if ($record->relationLoaded('activities')) {
                    $experienceActivities = $record->activities->filter(function ($activity) {
                        return $activity->relationLoaded('activityCategory') 
                            && $activity->activityCategory?->type === \App\Enums\ActivityCategoryTypeEnum::EXPERIENCE;
                    });
                } else {
                    $experienceActivities = $record->activities()
                        ->whereHas('activityCategory', function ($query) {
                            $query->where('type', \App\Enums\ActivityCategoryTypeEnum::EXPERIENCE->value);
                        })
                        ->get();
                }
                return $experienceActivities->isEmpty();
            })
            ->columnSpanFull();
    }

    private static function descriptionSection(): TextEntry
    {
        return TextEntry::make('description')
            ->label('📝 Description')
            ->icon('heroicon-o-document-text')
            ->hidden(fn($state) => empty($state))
            ->columnSpanFull();
    }

    private static function vehicleSection(): TextEntry
    {
        return TextEntry::make('vehicle_type_id')
            ->label('🚗 Vehicle')
            ->formatStateUsing(function ($state, $record) {
                if (!$record->vehicle_type_id && !$record->vehicle_usage_mode) {
                    return null;
                }
                
                $vehicleInfo = [];
                
                if ($record->vehicleType) {
                    $vehicleInfo[] = $record->vehicleType->name;
                }
                
                if ($record->vehicle_usage_mode) {
                    $vehicleInfo[] = "Mode: " . $record->vehicle_usage_mode->label();
                }
                
                return !empty($vehicleInfo) ? implode(' | ', $vehicleInfo) : null;
            })
            ->icon('heroicon-o-truck')
            ->color('primary')
            ->hidden(fn($state, $record) => !$record->vehicle_type_id && !$record->vehicle_usage_mode)
            ->columnSpanFull();
    }

    private static function companionSection(): TextEntry
    {
        return TextEntry::make('companion_category_id')
            ->label('👤 Companion')
            ->formatStateUsing(function ($state, $record) {
                if ($record->companion_hire_mode?->value !== 'daily') {
                    return null;
                }
                
                $companionInfo = [];
                
                if ($record->companionCategory) {
                    $companionInfo[] = $record->companionCategory->name;
                }
                
                if ($record->companion_hire_mode) {
                    $companionInfo[] = "Mode: " . $record->companion_hire_mode->label();
                }
                
                return !empty($companionInfo) ? implode(' | ', $companionInfo) : null;
            })
            ->icon('heroicon-o-user')
            ->color('success')
            ->hidden(fn($state, $record) => $record->companion_hire_mode?->value !== 'daily')
            ->columnSpanFull();
    }

    private static function editItineraryAction(): Action
    {
        return Action::make('Edit Itinerary Days')
            ->icon('heroicon-m-pencil-square')
            ->color('primary')
            ->url(fn(QuotationItinerary $quotationItinerary) => ItineraryResource::getUrl('edit', ['record' => $quotationItinerary->itinerary]));
    }

    private static function completeItineraryAction(): Action
    {
        return Action::make('Complete')
            ->icon('heroicon-m-check-circle')
            ->color('success')
            ->hidden(function (QuotationItinerary $quotationItinerary) {
                // Hide if no itinerary, already complete, or no days
                if (!$quotationItinerary->itinerary) {
                    return true;
                }

                if ($quotationItinerary->itinerary->is_complete) {
                    return true;
                }

                // Use loaded relationship instead of query
                $daysCount = $quotationItinerary->itinerary->relationLoaded('days') 
                    ? $quotationItinerary->itinerary->days->count() 
                    : $quotationItinerary->itinerary->days()->count();
                    
                if ($daysCount === 0) {
                    return true;
                }

                return false;
            })
            ->action(function (QuotationItinerary $quotationItinerary) {
                if ($quotationItinerary->itinerary) {
                    $quotationItinerary->itinerary->update(['is_complete' => true]);

                    // Generate breakdown automatically when itinerary is completed
                    $quotationItinerary->generateBreakdownFromItinerary();

                    Notification::make()
                        ->title('Itinerary completed successfully!')
                        ->body('Breakdown has been automatically generated. Redirecting to breakdown form...')
                        ->success()
                        ->send();

                    // Redirect to breakdown edit form
                    return redirect()->to(\App\Filament\App\Resources\QuotationItineraries\QuotationItineraryResource::getUrl('edit-breakdown', ['record' => $quotationItinerary]));
                }
            })
            ->requiresConfirmation()
            ->modalHeading('Complete Itinerary')
            ->modalDescription('Are you sure you want to mark this itinerary as complete?')
            ->modalSubmitActionLabel('Complete');
    }
}
