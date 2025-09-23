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
use Filament\Infolists\Components\RepeatableEntry;
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
                    ->persistTabInQueryString()
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
                            ->icon('heroicon-o-map')
                            ->badge(fn(QuotationItinerary $record) => $record->itinerary ? $record->itinerary->days()->count() . ' Days' : '0 Days')
                            ->badgeColor(fn(QuotationItinerary $record) => $record->itinerary ? 'success' : 'gray')
                            ->schema([
                                // Itinerary Summary
                                Section::make('Itinerary Summary')
                                    ->description('Quick overview of your travel plan')
                                    ->hidden(fn(QuotationItinerary $quotationItinerary) => !$quotationItinerary->itinerary)
                                    ->schema([
                                        Grid::make(4)
                                            ->schema([
                                                TextEntry::make('itinerary.travel_mode')
                                                    ->label('Travel Mode')
                                                    ->formatStateUsing(fn($state) => $state?->value ?? 'Not specified')
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
                                                    ->formatStateUsing(fn($state) => $state ? 'Complete' : 'In Progress')
                                                    ->icon(fn($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-clock')
                                                    ->color(fn($state) => $state ? 'success' : 'warning')
                                            ])
                                    ])
                                    ->collapsible(false),

                                // Create Itinerary Section (when no itinerary exists)
                                Section::make('Create Itinerary')
                                    ->description('Start building your travel plan')
                                    ->hidden(fn(QuotationItinerary $quotationItinerary) => $quotationItinerary->itinerary)
                                    ->schema([
                                        Grid::make(1)
                                            ->schema([
                                                Action::make('Create Itinerary')
                                            ->size(Size::ExtraLarge)
                                                    ->icon('heroicon-m-plus-circle')
                                            ->color('success')
                                            ->schema([
                                                Select::make('travel_mode')
                                                            ->options(TravelModeEnum::getOptions())
                                                            ->required()
                                                            ->placeholder('Select travel mode')
                                                    ])
                                                    ->action(function(array $data, QuotationItinerary $quotationItinerary){
                                                if (!$quotationItinerary->itinerary) {
                                                    $quotationItinerary->itinerary()->create([
                                                        'travel_mode' => $data['travel_mode'],
                                                        'creator_user_id' => Auth::user()->id,
                                                    ]);
                                                }
                                                    })
                                                    ->modalHeading('Create New Itinerary')
                                                    ->modalDescription('Choose the travel mode for your itinerary')
                                                    ->modalSubmitActionLabel('Create Itinerary')
                                            ])
                                            ->extraAttributes(['class' => 'flex justify-center items-center min-h-[200px]'])
                                    ])
                                    ->collapsible(false),

                                // Itinerary Days Display
                                Section::make('Itinerary Days')
                                    
                                    ->description('Your travel plan day by day')
                                    ->hidden(fn(QuotationItinerary $quotationItinerary) => !$quotationItinerary->itinerary)
                                    ->headerActions([
                                        Action::make('Edit Itinerary')
                                            ->icon('heroicon-m-pencil-square')
                                            ->color('primary')
                                            ->url(fn(QuotationItinerary $quotationItinerary) => ItineraryResource::getUrl('edit', ['record' => $quotationItinerary->itinerary]))
                                            ->openUrlInNewTab()
                                    ])
                                    ->schema([
                                        RepeatableEntry::make('itinerary.days')
                                            ->hiddenLabel()
                                            ->label('')
                                            ->schema([
                                                Grid::make(2)
                                                    ->schema([
                                                        TextEntry::make('day_number')
                                                            ->label('Day')
                                                            ->formatStateUsing(fn($state) => "Day {$state}")
                                                            ->icon('heroicon-o-calendar')
                                                            ->color('primary'),
                                                        
                                                        TextEntry::make('current_city_id')
                                                            ->label('City')
                                                            ->formatStateUsing(fn($state, $record) => $record->currentCity?->getTranslation('name', app()->getLocale()) ?? 'Unknown')
                                                            ->icon('heroicon-o-map-pin')
                                                            ->color('success'),
                                                    ]),
                                                
                                                Grid::make(2)
                                                    ->schema([
                                                        TextEntry::make('accommodation_id')
                                                            ->label('Accommodation')
                                                            ->formatStateUsing(fn($state, $record) => $record->accommodation?->getTranslation('name', app()->getLocale()) ?? 'Not specified')
                                                            ->icon('heroicon-o-building-office')
                                                            ->color('info'),
                                                        
                                                        TextEntry::make('accommodation_star_rating')
                                                            ->label('Star Rating')
                                                            ->formatStateUsing(fn($state) => $state ? str_repeat('★', $state->value) : 'Not rated')
                                                            ->icon('heroicon-o-star')
                                                            ->color('warning'),
                                                    ]),
                                                
                                                Grid::make(2)
                                                    ->schema([
                                                        TextEntry::make('has_vehicle')
                                                            ->label('Vehicle')
                                                            ->formatStateUsing(fn($state) => $state ? 'Yes' : 'No')
                                                            ->icon('heroicon-o-truck')
                                                            ->color(fn($state) => $state ? 'success' : 'gray'),
                                                        
                                                        TextEntry::make('has_tour_guide')
                                                            ->label('Tour Guide')
                                                            ->formatStateUsing(fn($state, $record) => $record->companions()->whereHas('companionCategory', function($query) {
                                                                $query->where('category_type', \App\Enums\CompanionCategoryEnum::TOUR_GUIDE->value);
                                                            })->exists() ? 'Yes' : 'No')
                                                            ->icon('heroicon-o-user')
                                                            ->color(fn($state, $record) => $record->companions()->whereHas('companionCategory', function($query) {
                                                                $query->where('category_type', \App\Enums\CompanionCategoryEnum::TOUR_GUIDE->value);
                                                            })->exists() ? 'success' : 'gray'),
                                                    ]),
                                                
                                                // Meals Section
                                                Grid::make(3)
                                                    ->schema([
                                                        TextEntry::make('itinerary.id')
                                                            ->label('🌅 Breakfast')
                                                            ->formatStateUsing(function($state, $record) {
                                                                $mealActivity = $record->activities()
                                                                    ->whereHas('activityCategory', function ($query) {
                                                                        $query->where('type', \App\Enums\ActivityCategoryTypeEnum::MEAL->value);
                                                                    })
                                                                    ->whereHas('meal', function ($query) {
                                                                        $query->where('meal_part', \App\Enums\MealPartEnum::BREAKFAST->value);
                                                                    })
                                                                    ->with('meal.mealType')
                                                                    ->first();
                                                                
                                                                return $mealActivity?->meal?->mealType?->name ?? null;
                                                            })
                                                            ->color('warning')
                                                            ->hidden(function($state, $record) {
                                                                $mealActivity = $record->activities()
                                                                    ->whereHas('activityCategory', function ($query) {
                                                                        $query->where('type', \App\Enums\ActivityCategoryTypeEnum::MEAL->value);
                                                                    })
                                                                    ->whereHas('meal', function ($query) {
                                                                        $query->where('meal_part', \App\Enums\MealPartEnum::BREAKFAST->value);
                                                                    })
                                                                    ->with('meal.mealType')
                                                                    ->first();
                                                                
                                                                return empty($mealActivity?->meal?->mealType?->name);
                                                            }),
                                                        
                                                        TextEntry::make('itinerary.id')
                                                            ->label('☀️ Lunch')
                                                            ->formatStateUsing(function($state, $record) {
                                                                $mealActivity = $record->activities()
                                                                    ->whereHas('activityCategory', function ($query) {
                                                                        $query->where('type', \App\Enums\ActivityCategoryTypeEnum::MEAL->value);
                                                                    })
                                                                    ->whereHas('meal', function ($query) {
                                                                        $query->where('meal_part', \App\Enums\MealPartEnum::LUNCH->value);
                                                                    })
                                                                    ->with('meal.mealType')
                                                                    ->first();
                                                                
                                                                return $mealActivity?->meal?->mealType?->name ?? null;
                                                            })
                                                            ->color('success')
                                                            ->hidden(function($state, $record) {
                                                                $mealActivity = $record->activities()
                                                                    ->whereHas('activityCategory', function ($query) {
                                                                        $query->where('type', \App\Enums\ActivityCategoryTypeEnum::MEAL->value);
                                                                    })
                                                                    ->whereHas('meal', function ($query) {
                                                                        $query->where('meal_part', \App\Enums\MealPartEnum::LUNCH->value);
                                                                    })
                                                                    ->with('meal.mealType')
                                                                    ->first();
                                                                
                                                                return empty($mealActivity?->meal?->mealType?->name);
                                                            }),
                                                        
                                                        TextEntry::make('itinerary.id')
                                                            ->label('🌙 Dinner')
                                                            ->formatStateUsing(function($state, $record) {
                                                                $mealActivity = $record->activities()
                                                                    ->whereHas('activityCategory', function ($query) {
                                                                        $query->where('type', \App\Enums\ActivityCategoryTypeEnum::MEAL->value);
                                                                    })
                                                                    ->whereHas('meal', function ($query) {
                                                                        $query->where('meal_part', \App\Enums\MealPartEnum::DINNER->value);
                                                                    })
                                                                    ->with('meal.mealType')
                                                                    ->first();
                                                                
                                                                return $mealActivity?->meal?->mealType?->name ?? null;
                                                            })
                                                            ->color('info')
                                                            ->hidden(function($state, $record) {
                                                                $mealActivity = $record->activities()
                                                                    ->whereHas('activityCategory', function ($query) {
                                                                        $query->where('type', \App\Enums\ActivityCategoryTypeEnum::MEAL->value);
                                                                    })
                                                                    ->whereHas('meal', function ($query) {
                                                                        $query->where('meal_part', \App\Enums\MealPartEnum::DINNER->value);
                                                                    })
                                                                    ->with('meal.mealType')
                                                                    ->first();
                                                                
                                                                return empty($mealActivity?->meal?->mealType?->name);
                                                            }),
                                                    ])
                                                    ->columnSpanFull(),
                                                
                                                // Tickets Section
                                                TextEntry::make('itinerary.id')
                                                    ->label('🎫 Tickets')
                                                    ->formatStateUsing(function($state, $record) {
                                                        // Get tickets directly from activities
                                                        $ticketActivities = $record->activities()
                                                            ->whereHas('activityCategory', function ($query) {
                                                                $query->where('type', \App\Enums\ActivityCategoryTypeEnum::TICKET->value);
                                                            })
                                                            ->with('ticket.toCity')
                                                            ->get();
                                                        
                                                        if ($ticketActivities->isEmpty()) {
                                                            return 'No tickets';
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
                                                                
                                                                // Get appropriate icon based on TransportModeEnum
                                                                $icon = match($transportMode) {
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
                                                    ->columnSpanFull(),
                                                
                                                // Attractions Section
                                                TextEntry::make('itinerary.id')
                                                    ->label('🏛️ Attractions')
                                                    ->formatStateUsing(function($state, $record) {
                                                        // Get attractions directly from activities
                                                        $attractionActivities = $record->activities()
                                                            ->whereHas('activityCategory', function ($query) {
                                                                $query->where('type', \App\Enums\ActivityCategoryTypeEnum::ATTRACTION->value);
                                                            })
                                                            ->with(['attraction.attraction', 'attraction.subAttractions.subAttraction'])
                                                            ->get();
                                                        
                                                        if ($attractionActivities->isEmpty()) {
                                                            return 'No attractions';
                                                        }
                                                        
                                                        $attractionInfo = [];
                                                        foreach ($attractionActivities as $activity) {
                                                            if ($activity->attraction) {
                                                                $attractionName = $activity->attraction->attraction?->name ?? 'Unknown';
                                                                $isOutview = $activity->attraction->is_outview ? ' (Outview)' : '';
                                                                
                                                                // Get sub-attractions
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
                                                    ->columnSpanFull(),
                                                
                                                // Experiences Section
                                                TextEntry::make('itinerary.id')
                                                    ->label('🎭 Experiences')
                                                    ->formatStateUsing(function($state, $record) {
                                                        // Get experiences directly from activities
                                                        $experienceActivities = $record->activities()
                                                            ->whereHas('activityCategory', function ($query) {
                                                                $query->where('type', \App\Enums\ActivityCategoryTypeEnum::EXPERIENCE->value);
                                                            })
                                                            ->with('experience.experience')
                                                            ->get();
                                                        
                                                        if ($experienceActivities->isEmpty()) {
                                                            return 'No experiences';
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
                                                    ->columnSpanFull(),
                                                
                                                TextEntry::make('description')
                                                    ->label('Description')
                                                    ->formatStateUsing(fn($state) => $state ?? 'No description')
                                                    ->icon('heroicon-o-document-text')
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(1)
                                    ])
                                    ->collapsible()
                                    ->collapsed(false)
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
            ]);
    }
}
