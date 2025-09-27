<?php

namespace App\Filament\App\Resources\QuotationItineraries\Schemas;

use App\Enums\TravelModeEnum;
use App\Filament\App\Resources\Itineraries\ItineraryResource;
use App\Models\Tenants\Itinerary;
use App\Models\Tenants\QuotationItinerary;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
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
                        self::informationTab(),
                        self::itineraryTab(),
                        self::breakdownTab(),
                        self::offersTab(),
                    ]),
            ]);
    }

    private static function informationTab(): Tab
    {
        return Tab::make('Information')
                            ->icon('heroicon-o-information-circle')
                            ->badge('✓')
                            ->badgeColor('success')
                            ->schema([
                                // Inquiry Information
                                Section::make('Inquiry Information')
                                    ->description('Basic inquiry details and information')
                                    ->icon('heroicon-o-document-text')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('quotation.id')
                                                    ->label('Title')
                                                    ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->getTranslation('title', app()->getLocale()) ?? 'No title')
                                                    ->icon('heroicon-o-tag')
                                                    ->color('primary'),

                                                TextEntry::make('quotation.id')
                                                    ->label('Inquiry Number')
                                                    ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->number ?? 'No number')
                                                    ->icon('heroicon-o-hashtag')
                                                    ->color('info'),
                                            ]),

                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('quotation.id')
                                                    ->label('Contact')
                                                    ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->contact?->full_name ?? 'No contact')
                                                    ->icon('heroicon-o-user')
                                                    ->color('success'),

                                                TextEntry::make('quotation.id')
                                                    ->label('Requested Currency')
                                                    ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->requestedCurrency?->name ?? 'Not specified')
                                                    ->icon('heroicon-o-banknotes')
                                                    ->color('info'),
                                            ]),

                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('quotation.id')
                                                    ->label('Reference')
                                                    ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->reference ?? 'No reference')
                                                    ->icon('heroicon-o-link')
                                                    ->color('warning'),

                                                TextEntry::make('quotation.id')
                                                    ->label('Date Type')
                                                    ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->inquiryItinerary?->date_type?->value ?? 'Not specified')
                                                    ->icon('heroicon-o-calendar')
                                                    ->color('primary'),
                                            ]),

                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('quotation.id')
                                                    ->label('From Date')
                                                    ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->inquiryItinerary?->from_date?->format('Y-m-d') ?? 'Not specified')
                                                    ->icon('heroicon-o-calendar-days')
                                                    ->color('success'),

                                                TextEntry::make('quotation.id')
                                                    ->label('To Date')
                                                    ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->inquiryItinerary?->to_date?->format('Y-m-d') ?? 'Not specified')
                                                    ->icon('heroicon-o-calendar-days')
                                                    ->color('warning'),
                                            ]),

                                        TextEntry::make('quotation.id')
                                            ->label('Description')
                                            ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->getTranslation('description', app()->getLocale()) ?? 'No description')
                                            ->icon('heroicon-o-document-text')
                                            ->columnSpanFull(),
                                    ]),

                                // Quotation Information
                                Section::make('Quotation Information')
                                    ->description('Quotation and pricing details')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('quotation.number')
                                                    ->label('Quotation Number')
                                                    ->icon('heroicon-o-hashtag')
                                                    ->color('primary'),

                                                TextEntry::make('quotation.exchange_rate')
                                                    ->label('Exchange Rate')
                                                    ->formatStateUsing(fn($state) => $state ? number_format($state, 4) : 'Not specified')
                                                    ->icon('heroicon-o-arrow-path')
                                                    ->color('info'),
                                            ]),

                                        TextEntry::make('quotation.expire_date')
                                            ->label('Expiry Date')
                                            ->date()
                                            ->icon('heroicon-o-calendar-days')
                                            ->color('danger')
                                            ->columnSpanFull(),

                                        TextEntry::make('quotation.description')
                                            ->label('Description')
                                            ->formatStateUsing(fn($state) => $state ?? 'No description')
                                            ->icon('heroicon-o-document-text')
                                            ->columnSpanFull(),

                                        TextEntry::make('quotation.internal_note')
                                            ->label('Internal Note')
                                            ->formatStateUsing(fn($state) => $state ?? 'No internal note')
                                            ->icon('heroicon-o-exclamation-triangle')
                                            ->color('warning')
                                            ->columnSpanFull(),
                                    ]),
                            ]);
    }

    private static function itineraryTab(): Tab
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
                                // Itinerary Summary
                                Section::make('Itinerary Summary')
                                    ->description('Quick overview of your travel plan')
                                    ->hidden(fn(QuotationItinerary $quotationItinerary) => !$quotationItinerary->itinerary)
                                    ->headerActions([
                                        Action::make('Edit Itinerary')
                                            ->icon('heroicon-m-pencil-square')
                                            ->color('primary')
                                            ->url(fn(QuotationItinerary $quotationItinerary) => ItineraryResource::getUrl('edit', ['record' => $quotationItinerary->itinerary])),

                                        Action::make('Complete')
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

                                                if ($quotationItinerary->itinerary->days()->count() === 0) {
                                                    return true;
                                                }

                                                return false;
                                            })
                                            ->action(function (QuotationItinerary $quotationItinerary) {
                                                if ($quotationItinerary->itinerary) {
                                                    $quotationItinerary->itinerary->update(['is_complete' => true]);
                                                }
                                            })
                                            ->requiresConfirmation()
                                            ->modalHeading('Complete Itinerary')
                                            ->modalDescription('Are you sure you want to mark this itinerary as complete?')
                                            ->modalSubmitActionLabel('Complete')
                                    ])
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
                                                    ->formatStateUsing(fn($state) => $state ? 'Completed' : 'In Progress')
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
                                                    ->action(function (array $data, QuotationItinerary $quotationItinerary) {
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
                                    ->compact()
                                    ->hidden(function (QuotationItinerary $quotationItinerary) {
                                        // Hide if no itinerary or no days
                                        if (!$quotationItinerary->itinerary) {
                                            return true;
                                        }

                                        if ($quotationItinerary->itinerary->days()->count() === 0) {
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
                                                    ->heading(fn($record) => "Day {$record->day_number}")
                                                    ->description(function ($record) {
                                                        $items = [];
                                                        $items[] = '📍 ' . ($record->accommodationCity?->name ?? 'Unknown City');

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

                                                        // Check for Breakfast
                                                        $breakfastActivity = $record->activities()
                                                            ->whereHas('activityCategory', function ($query) {
                                                                $query->where('type', \App\Enums\ActivityCategoryTypeEnum::MEAL->value);
                                                            })
                                                            ->whereHas('meal', function ($query) {
                                                                $query->where('meal_part', \App\Enums\MealPartEnum::BREAKFAST->value);
                                                            })
                                                            ->with('meal.mealType')
                                                            ->first();

                                                        if ($breakfastActivity?->meal?->mealType?->name) {
                                                            $bldItems[] = 'B';
                                                        }

                                                        // Check for Lunch
                                                        $lunchActivity = $record->activities()
                                                            ->whereHas('activityCategory', function ($query) {
                                                                $query->where('type', \App\Enums\ActivityCategoryTypeEnum::MEAL->value);
                                                            })
                                                            ->whereHas('meal', function ($query) {
                                                                $query->where('meal_part', \App\Enums\MealPartEnum::LUNCH->value);
                                                            })
                                                            ->with('meal.mealType')
                                                            ->first();

                                                        if ($lunchActivity?->meal?->mealType?->name) {
                                                            $bldItems[] = 'L';
                                                        }

                                                        // Check for Dinner
                                                        $dinnerActivity = $record->activities()
                                                            ->whereHas('activityCategory', function ($query) {
                                                                $query->where('type', \App\Enums\ActivityCategoryTypeEnum::MEAL->value);
                                                            })
                                                            ->whereHas('meal', function ($query) {
                                                                $query->where('meal_part', \App\Enums\MealPartEnum::DINNER->value);
                                                            })
                                                            ->with('meal.mealType')
                                                            ->first();

                                                        if ($dinnerActivity?->meal?->mealType?->name) {
                                                            $bldItems[] = 'D';
                                                        }

                                                        if (!empty($bldItems)) {
                                                            $items[] = '🍽️ ' . implode('', $bldItems);
                                                        }

                                                        return implode(' | ', $items);
                                                    })
                                                    ->icon('heroicon-o-calendar')
                                                    ->collapsible()
                                                    ->collapsed()
                                                    ->schema([

                                                        // Meals Section
                                                        Grid::make(3)
                                                            ->schema([
                                                                TextEntry::make('itinerary.id')
                                                                    ->label('🌅 Breakfast')
                                                                    ->formatStateUsing(function ($state, $record) {
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
                                                                    ->hidden(function ($state, $record) {
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
                                                                    ->formatStateUsing(function ($state, $record) {
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
                                                                    ->hidden(function ($state, $record) {
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
                                                                    ->formatStateUsing(function ($state, $record) {
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
                                                                    ->hidden(function ($state, $record) {
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
                                                            ->formatStateUsing(function ($state, $record) {
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
                                                            ->columnSpanFull(),

                                                        // Attractions Section
                                                        TextEntry::make('itinerary.id')
                                                            ->label('🏛️ Attractions')
                                                            ->formatStateUsing(function ($state, $record) {
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
                                                            ->formatStateUsing(function ($state, $record) {
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
                                            ])
                                            ->columns(1)
                                    ])


                            ]);
    }

    private static function breakdownTab(): Tab
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
                                // Create Breakdown Section (when no breakdown exists)
                                Section::make('Create Breakdown')
                                    ->description('Start building your cost breakdown')
                                    ->hidden(fn(QuotationItinerary $quotationItinerary) => $quotationItinerary->breakdown)
                                    ->schema([
                                        Grid::make(1)
                                            ->schema([
                                                Action::make('Create Breakdown')
                                                    ->size(Size::ExtraLarge)
                                                    ->icon('heroicon-m-plus-circle')
                                                    ->color('primary')
                                                    ->action(function (QuotationItinerary $quotationItinerary) {
                                                        // Check if itinerary is complete
                                                        if (!$quotationItinerary->itinerary || !$quotationItinerary->itinerary->days->count()) {
                                                            Notification::make()
                                                                ->title('Incomplete Itinerary')
                                                                ->body('Please complete the itinerary first before generating breakdown.')
                                                                ->warning()
                                                                ->send();
                                                            return;
                                                        }

                                                        // Generate breakdown from itinerary
                                                        $quotationItinerary->generateBreakdownFromItinerary();
                                                        
                                                        Notification::make()
                                                            ->title('Breakdown Generated')
                                                            ->body('Cost breakdown has been successfully generated from the itinerary.')
                                                            ->success()
                                                            ->send();
                                                    })
                                                    ->modalHeading('Create New Breakdown')
                                                    ->modalDescription('Create a detailed cost breakdown for this quotation itinerary')
                                                    ->modalSubmitActionLabel('Create Breakdown')
                                            ])
                                            ->extraAttributes(['class' => 'flex justify-center items-center min-h-[200px]'])
                                    ])
                                    ->collapsible(false),

                                // Breakdown Overview (when breakdown exists)
                                Section::make('Breakdown Overview')
                                    ->description('Cost breakdown summary and details')
                                    ->hidden(fn(QuotationItinerary $quotationItinerary) => !$quotationItinerary->breakdown)
                                    ->headerActions([
                                        Action::make('complete_breakdown')
                                            ->label('Complete')
                                            ->icon('heroicon-m-check-circle')
                                            ->color('success')
                                            ->hidden(function (QuotationItinerary $quotationItinerary) {
                                                return !$quotationItinerary->breakdown || $quotationItinerary->breakdown->is_completed;
                                            })
                                            ->action(function (QuotationItinerary $quotationItinerary) {
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
                                            ->modalDescription('Are you sure you want to mark this breakdown as complete?')
                                            ->modalSubmitActionLabel('Complete'),
                                        Action::make('edit_breakdown')
                                            ->label('Edit')
                                            ->icon('heroicon-m-pencil-square')
                                            ->color('gray')
                                            ->url(fn(QuotationItinerary $quotationItinerary) => \App\Filament\App\Resources\QuotationItineraries\QuotationItineraryResource::getUrl('edit-breakdown', ['record' => $quotationItinerary])),
                                        Action::make('delete_breakdown')
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
                                                    Notification::make()
                                                        ->title('Breakdown deleted successfully!')
                                                        ->success()
                                                        ->send();
                                                }
                                            }),
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
                                    ->collapsible(false),

                                // Vehicle Types Section
                                Section::make('Vehicle Types')
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

                                                        TextEntry::make('extra_hour_price')
                                                            ->label('Extra Hour Price')
                                                            ->money('CNY')
                                                            ->icon('heroicon-o-clock')
                                                            ->color('info'),
                                                    ])
                                            ])
                                    ])
                                    ->collapsible(),

                                // Tickets Section
                                Section::make('Tickets')
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
                                    ->collapsible(),

                                // Meals Section
                                Section::make('Meals')
                                    ->description('Meal types and quantities')
                                    ->hidden(fn(QuotationItinerary $quotationItinerary) => !$quotationItinerary->breakdown)
                                    ->schema([
                                        RepeatableEntry::make('breakdown.meals')
                                            ->hiddenLabel()
                                            ->contained(false)
                                            ->schema([
                                                Grid::make(3)
                                                    ->schema([
                                                        TextEntry::make('mealType.name')
                                                            ->label('Meal Type')
                                                            ->icon('heroicon-o-cake')
                                                            ->color('primary'),

                                                        TextEntry::make('qty')
                                                            ->label('Quantity')
                                                            ->numeric()
                                                            ->icon('heroicon-o-hashtag')
                                                            ->color('success'),

                                                        TextEntry::make('price')
                                                            ->label('Price')
                                                            ->money('CNY')
                                                            ->icon('heroicon-o-currency-dollar')
                                                            ->color('warning'),
                                                    ])
                                            ])
                                    ])
                                    ->collapsible(),

                                // Experiences Section
                                Section::make('Experiences')
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
                                                            ->badge()
                                                            ->color('info'),
                                                    ])
                                            ])
                                    ])
                                    ->collapsible(),

                                // Accommodations Section
                                Section::make('Accommodations')
                                    ->description('Hotel accommodations and room details')
                                    ->hidden(fn(QuotationItinerary $quotationItinerary) => !$quotationItinerary->breakdown)
                                    ->schema([
                                        RepeatableEntry::make('breakdown.accommodations')
                                            ->hiddenLabel()
                                            ->contained(false)
                                            ->schema([
                                                Grid::make(3)
                                                    ->schema([
                                                        TextEntry::make('accommodation.name')
                                                            ->label('Accommodation')
                                                            ->icon('heroicon-o-home')
                                                            ->color('primary'),

                                                        TextEntry::make('city.name')
                                                            ->label('City')
                                                            ->icon('heroicon-o-map-pin')
                                                            ->color('success'),

                                                        TextEntry::make('nights_qty')
                                                            ->label('Nights')
                                                            ->numeric()
                                                            ->icon('heroicon-o-moon')
                                                            ->color('warning'),
                                                    ]),

                                                // Room Categories
                                                RepeatableEntry::make('rooms')
                                                    ->label('Room Categories')
                                                    ->hiddenLabel()
                                                    ->contained(false)
                                                    ->schema([
                                                        Grid::make(2)
                                                            ->schema([
                                                                TextEntry::make('roomCategory.name')
                                                                    ->label('Room Type')
                                                                    ->icon('heroicon-o-home')
                                                                    ->color('primary'),

                                                                TextEntry::make('price')
                                                                    ->label('Price')
                                                                    ->money('CNY')
                                                                    ->icon('heroicon-o-currency-dollar')
                                                                    ->color('success'),
                                                            ])
                                                    ])
                                            ])
                                    ])
                                    ->collapsible(),

                                // Attractions Section
                                Section::make('Attractions')
                                    ->description('Tourist attractions and entry fees')
                                    ->hidden(fn(QuotationItinerary $quotationItinerary) => !$quotationItinerary->breakdown)
                                    ->schema([
                                        RepeatableEntry::make('breakdown.attractions')
                                            ->hiddenLabel()
                                            ->contained(false)
                                            ->schema([
                                                Grid::make(3)
                                                    ->schema([
                                                        TextEntry::make('attraction.name')
                                                            ->label('Attraction')
                                                            ->icon('heroicon-o-building-library')
                                                            ->color('primary'),

                                                        TextEntry::make('city.name')
                                                            ->label('City')
                                                            ->icon('heroicon-o-map-pin')
                                                            ->color('success'),

                                                        TextEntry::make('entry_price')
                                                            ->label('Entry Price')
                                                            ->money('CNY')
                                                            ->icon('heroicon-o-currency-dollar')
                                                            ->color('warning'),
                                                    ]),

                                                TextEntry::make('is_outview')
                                                    ->label('Outview')
                                                    ->formatStateUsing(fn($state) => $state ? 'Yes' : 'No')
                                                    ->badge()
                                                    ->color(fn($state) => $state ? 'warning' : 'success'),

                                                // Sub Attractions
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
                                                                    ->color('primary'),

                                                                TextEntry::make('price')
                                                                    ->label('Price')
                                                                    ->money('CNY')
                                                                    ->icon('heroicon-o-currency-dollar')
                                                                    ->color('success'),
                                                            ])
                                                    ])
                                            ])
                                    ])
                                    ->collapsible(),

                                // Companions Section
                                Section::make('Companions')
                                    ->description('Tour guides and companion services')
                                    ->hidden(fn(QuotationItinerary $quotationItinerary) => !$quotationItinerary->breakdown)
                                    ->schema([
                                        RepeatableEntry::make('breakdown.companions')
                                            ->hiddenLabel()
                                            ->contained(false)
                                            ->schema([
                                                Grid::make(5)
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

                                                        TextEntry::make('pickup_price')
                                                            ->label('Pickup Price')
                                                            ->money('CNY')
                                                            ->icon('heroicon-o-truck')
                                                            ->color('info'),

                                                        TextEntry::make('per_hour_price')
                                                            ->label('Per Hour Price')
                                                            ->money('CNY')
                                                            ->icon('heroicon-o-clock')
                                                            ->color('gray'),
                                                    ])
                                            ])
                                    ])
                                    ->collapsible(),

                                // Expenses Section
                                Section::make('Additional Expenses')
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
                                                            ->badge()
                                                            ->color('info'),
                                                    ])
                                            ])
                                    ])
                                    ->collapsible(),

                            ]);
    }

    private static function offersTab(): Tab
    {
        return Tab::make('Offers')
            ->schema([
                // ...
            ]);
    }
}
