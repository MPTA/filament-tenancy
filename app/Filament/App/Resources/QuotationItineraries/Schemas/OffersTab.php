<?php

namespace App\Filament\App\Resources\QuotationItineraries\Schemas;

use App\Models\Tenants\QuotationItinerary;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Exceptions\Halt;

class OffersTab
{
    public static function getTab(): Tab
    {
        return Tab::make('Offers')
            ->icon('heroicon-o-ticket')
            ->schema([
                self::addNewOfferSection(),
                self::noOffersMessage(),
                self::offerGroupsList(),
            ]);
    }

    private static function addNewOfferSection(): Section
    {
        return Section::make('Offer Groups')
            ->description('Manage offer groups and create new ones')
            ->icon('heroicon-o-cog-6-tooth')
            ->schema([])
            ->contained(false)
            ->headerActions([
                self::addNewOfferAction(),
            ]);
    }

    private static function addNewOfferAction(): Action
    {
        return Action::make('add_new_offer_group')
            ->label(function (QuotationItinerary $record) {
                if (!$record->breakdown) {
                    return 'Create Breakdown First';
                }
                if (!$record->breakdown->is_completed) {
                    return 'Complete Breakdown to Create Offer';
                }
                return 'Add new Offer group';
            })
            ->icon('heroicon-o-plus')
            ->color(function (QuotationItinerary $record) {
                if (!$record->breakdown || !$record->breakdown->is_completed) {
                    return 'gray';
                }
                return 'primary';
            })
            ->disabled(function (QuotationItinerary $record) {
                return !$record->breakdown || !$record->breakdown->is_completed;
            })
            ->tooltip(function (QuotationItinerary $record) {
                if (!$record->breakdown) {
                    return 'Please create a breakdown before creating an offer group.';
                }
                if (!$record->breakdown->is_completed) {
                    return 'Please complete the breakdown before creating an offer group.';
                }
                return null;
            })
            ->schema([
                self::driverSettingsSection(),
                self::companionsSection(),
            ])
            ->action(function (array $data, QuotationItinerary $record) {
                try {
                    $offerGroup = $record->quotationOfferGroups()->create([
                        'is_include_driver_meal' => $data['is_include_driver_meal'] ?? false,
                        'is_include_driver_hotel' => $data['is_include_driver_hotel'] ?? false,
                        'is_driver_stay_same_hotel' => $data['is_driver_stay_same_hotel'] ?? false,
                        'is_driver_same_meal' => $data['is_driver_same_meal'] ?? false,
                        'driver_room_category_id' => $data['driver_room_category_id'] ?? null,
                    ]);

                    if (isset($data['companions']) && is_array($data['companions'])) {
                        foreach ($data['companions'] as $companionData) {
                            if (!empty($companionData['companion_type_id'])) {
                                $offerGroup->quotationOfferGroupCompanions()->create([
                                    'companion_type_id' => $companionData['companion_type_id'],
                                    'is_stay_same_hotel' => $companionData['is_stay_same_hotel'] ?? false,
                                    'is_same_meal' => $companionData['is_same_meal'] ?? false,
                                    'room_category_id' => $companionData['room_category_id'] ?? null,
                                    'living_city_id' => $companionData['living_city_id'] ?? null,
                                ]);
                            }
                        }
                    }

                    // Calculate all costs from breakdown
                    $offerGroup->calculateAllCostsFromBreakdown();

                    $record->refresh();

                    Notification::make()
                        ->title('Offer Group Created Successfully!')
                        ->body('The offer group and companions have been saved.')
                        ->success()
                        ->send();

                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Error Creating Offer Group')
                        ->body('An error occurred while saving the offer group: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    private static function driverSettingsSection(): Section
    {
        return Section::make('Driver Settings')
            ->description('Configure driver-related costs and accommodations')
            ->icon('heroicon-o-user')
            ->schema([
                // 1. Include Driver Meal
                Checkbox::make('is_include_driver_meal')
                    ->label('Include Driver Meal')
                    ->helperText('Should driver meal cost be included in calculations?')
                    ->default(false)
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set) {
                        if (!$state) {
                            $set('is_driver_same_meal', false);
                        }
                    }),

                // 2. Driver Same Meal
                Checkbox::make('is_driver_same_meal')
                    ->label('Driver Same Meal')
                    ->helperText('Does the driver have the same meals as passengers?')
                    ->default(false)
                    ->visible(fn ($get) => $get('is_include_driver_meal')),

                // 3. Include Driver Hotel
                Checkbox::make('is_include_driver_hotel')
                    ->label('Include Driver Hotel')
                    ->helperText('Should driver hotel cost be included in calculations?')
                    ->default(false)
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set) {
                        if (!$state) {
                            $set('is_driver_stay_same_hotel', false);
                            $set('driver_room_category_id', null);
                        }
                    }),

                // 4. Driver Stays Same Hotel
                Checkbox::make('is_driver_stay_same_hotel')
                    ->label('Driver Stays Same Hotel')
                    ->helperText('Does the driver stay in the same hotel as passengers?')
                    ->default(false)
                    ->visible(fn ($get) => $get('is_include_driver_hotel'))
                    ->reactive(),

                // 5. Driver Room Type
                Select::make('driver_room_category_id')
                    ->label('Driver Room Type')
                    ->options(function (QuotationItinerary $record) {
                        if (!$record->breakdown) return [];
                        $roomCategoryIds = $record->breakdown
                            ->accommodations()
                            ->with('rooms')
                            ->get()
                            ->pluck('rooms')
                            ->flatten()
                            ->pluck('room_category_id')
                            ->unique()
                            ->filter();
                        return \App\Models\Base\RoomCategory::whereIn('id', $roomCategoryIds)
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->helperText('Select driver room type')
                    ->required(fn ($get) => $get('is_driver_stay_same_hotel'))
                    ->visible(fn ($get) => $get('is_driver_stay_same_hotel')),
            ])
            ->collapsible()
            ->collapsed(false);
    }

    private static function companionsSection(): Section
    {
        return Section::make('Companions')
            ->description('Add and manage travel companions')
            ->icon('heroicon-o-users')
            ->schema([
                Repeater::make('companions')
                    ->label('Companions')
                    ->schema([
                        Select::make('companion_type_id')
                            ->label('Companion Type')
                            ->options(function (QuotationItinerary $record) {
                                if (!$record->breakdown) {
                                    return \App\Models\Tenants\CompanionType::all()->pluck('name', 'id');
                                }
                                $companionTypeIds = $record->breakdown
                                    ->companions()
                                    ->pluck('companion_type_id')
                                    ->unique()
                                    ->filter();
                                if ($companionTypeIds->isEmpty()) {
                                    return \App\Models\Tenants\CompanionType::all()->pluck('name', 'id');
                                }
                                return \App\Models\Tenants\CompanionType::whereIn('id', $companionTypeIds)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),

                        Checkbox::make('is_same_meal')
                            ->label('Same Meal')
                            ->helperText('Does this companion have the same meals?')
                            ->default(false)
                            ->columnSpan(1),

                        Checkbox::make('is_stay_same_hotel')
                            ->label('Stay Same Hotel')
                            ->helperText('Does this companion stay in the same hotel?')
                            ->default(false)
                            ->reactive()
                            ->columnSpan(1),

                        Select::make('room_category_id')
                            ->label('Room Type')
                            ->options(function (QuotationItinerary $record) {
                                if (!$record->breakdown) return [];
                                $roomCategoryIds = $record->breakdown
                                    ->accommodations()
                                    ->with('rooms')
                                    ->get()
                                    ->pluck('rooms')
                                    ->flatten()
                                    ->pluck('room_category_id')
                                    ->unique()
                                    ->filter();
                                return \App\Models\Base\RoomCategory::whereIn('id', $roomCategoryIds)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->helperText('Select room type')
                            ->required(fn ($get) => $get('is_stay_same_hotel'))
                            ->visible(fn ($get) => $get('is_stay_same_hotel'))
                            ->columnSpan(1),

                        Select::make('living_city_id')
                            ->label('Living City')
                            ->options(function () {
                                $tenantSetting = \App\Models\TenantSetting::where('tenant_id', tenant('id'))->first();
                                if (!$tenantSetting || !$tenantSetting->country_id) {
                                    return \App\Models\Base\City::all()->pluck('name', 'id');
                                }
                                return \App\Models\Base\City::whereHas('province', function ($query) use ($tenantSetting) {
                                    $query->where('country_id', $tenantSetting->country_id);
                                })->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->columnSpan(2),
                    ])
                    ->columns(2)
                    ->addActionLabel('Add Companion')
                    ->defaultItems(0)
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => 
                        $state['companion_type_id'] ? 
                        \App\Models\Tenants\CompanionType::find($state['companion_type_id'])?->name : 
                        'New Companion'
                    ),
            ])
            ->collapsible()
            ->collapsed(false);
    }

    private static function noOffersMessage(): TextEntry
    {
        return TextEntry::make('id')
            ->label('')
            ->hiddenLabel()
            ->formatStateUsing(fn() => 'No offers have been registered yet.')
            ->icon('heroicon-o-information-circle')
            ->color('gray')
            ->hidden(fn(QuotationItinerary $record) => $record->quotationOfferGroups()->count() > 0);
    }

    private static function offerGroupsList(): RepeatableEntry
    {
        return RepeatableEntry::make('quotationOfferGroups')
            ->contained(false)
            ->label('')
            ->hidden(fn(QuotationItinerary $record) => $record->quotationOfferGroups()->count() === 0)
            ->schema([
                self::offerGroupSection(),
            ])
            ->columns(1);
    }

    private static function offerGroupSection(): Section
    {
        return Section::make()
            ->heading(fn($record) => 'Offer Group #' . $record->id)
            ->description(function ($record) {
                $info = [];
                if ($record->is_include_driver_meal) {
                    $info[] = 'Driver meal included';
                }
                if ($record->is_include_driver_hotel) {
                    $info[] = 'Driver hotel included';
                }
                if (empty($info)) {
                    $info[] = 'No driver costs';
                }
                $info[] = $record->quotationOfferGroupCompanions->count() . ' companion(s)';
                return implode(' • ', $info);
            })
            ->icon('heroicon-o-cog-6-tooth')
            ->headerActions([
                self::viewOfferGroupAction(),
                self::editOfferGroupAction(),
                self::deleteOfferGroupAction(),
            ])
            ->footerActions([
                self::createOfferAction(),
            ])
            ->schema([
                self::driverInfoGrid(),
                self::companionsTable(),
                self::offersList(),
            ]);
    }

    private static function viewOfferGroupAction(): Action
    {
        return Action::make('view_offer_group')
            ->label('View')
            ->icon('heroicon-m-eye')
            ->color('info')
            ->size('sm')
            ->infolist(OfferGroupInfolist::getSchema())
            ->modalHeading('Offer Group Details')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close');
    }

    private static function editOfferGroupAction(): Action
    {
        return Action::make('edit_offer_group')
            ->label('Edit')
            ->icon('heroicon-m-pencil-square')
            ->color('primary')
            ->size('sm')
            ->schema([
                Section::make('Driver Settings')
                    ->schema([
                        // 1. Include Driver Meal
                        Checkbox::make('is_include_driver_meal')
                            ->label('Include Driver Meal')
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set) {
                                if (!$state) {
                                    $set('is_driver_same_meal', false);
                                }
                            }),

                        // 2. Driver Same Meal
                        Checkbox::make('is_driver_same_meal')
                            ->label('Driver Same Meal')
                            ->visible(fn ($get) => $get('is_include_driver_meal')),

                        // 3. Include Driver Hotel
                        Checkbox::make('is_include_driver_hotel')
                            ->label('Include Driver Hotel')
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set) {
                                if (!$state) {
                                    $set('is_driver_stay_same_hotel', false);
                                    $set('driver_room_category_id', null);
                                }
                            }),

                        // 4. Driver Stays Same Hotel
                        Checkbox::make('is_driver_stay_same_hotel')
                            ->label('Driver Stays Same Hotel')
                            ->visible(fn ($get) => $get('is_include_driver_hotel'))
                            ->reactive(),

                        // 5. Driver Room Type
                        Select::make('driver_room_category_id')
                            ->label('Driver Room Type')
                            ->options(function ($record) {
                                // Get the quotation itinerary from the offer group
                                $quotationItinerary = $record->quotationItinerary;
                                if (!$quotationItinerary || !$quotationItinerary->breakdown) return [];
                                
                                $roomCategoryIds = $quotationItinerary->breakdown
                                    ->accommodations()
                                    ->with('rooms')
                                    ->get()
                                    ->pluck('rooms')
                                    ->flatten()
                                    ->pluck('room_category_id')
                                    ->unique()
                                    ->filter();
                                return \App\Models\Base\RoomCategory::whereIn('id', $roomCategoryIds)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required(fn ($get) => $get('is_driver_stay_same_hotel'))
                            ->visible(fn ($get) => $get('is_driver_stay_same_hotel')),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                Section::make('Companions')
                    ->description('Manage travel companions')
                    ->icon('heroicon-o-users')
                    ->schema([
                        Repeater::make('companions')
                            ->label('Companions')
                            ->schema([
                                Select::make('companion_type_id')
                                    ->label('Companion Type')
                                    ->options(function ($record) {
                                        $quotationItinerary = $record->quotationItinerary;
                                        if (!$quotationItinerary || !$quotationItinerary->breakdown) {
                                            return \App\Models\Tenants\CompanionType::all()->pluck('name', 'id');
                                        }
                                        $companionTypeIds = $quotationItinerary->breakdown
                                            ->companions()
                                            ->pluck('companion_type_id')
                                            ->unique()
                                            ->filter();
                                        if ($companionTypeIds->isEmpty()) {
                                            return \App\Models\Tenants\CompanionType::all()->pluck('name', 'id');
                                        }
                                        return \App\Models\Tenants\CompanionType::whereIn('id', $companionTypeIds)
                                            ->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(1),

                                Checkbox::make('is_same_meal')
                                    ->label('Same Meal')
                                    ->helperText('Does this companion have the same meals?')
                                    ->default(false)
                                    ->columnSpan(1),

                                Checkbox::make('is_stay_same_hotel')
                                    ->label('Stay Same Hotel')
                                    ->helperText('Does this companion stay in the same hotel?')
                                    ->default(false)
                                    ->reactive()
                                    ->columnSpan(1),

                                Select::make('room_category_id')
                                    ->label('Room Type')
                                    ->options(function ($record) {
                                        $quotationItinerary = $record->quotationItinerary;
                                        if (!$quotationItinerary || !$quotationItinerary->breakdown) return [];
                                        $roomCategoryIds = $quotationItinerary->breakdown
                                            ->accommodations()
                                            ->with('rooms')
                                            ->get()
                                            ->pluck('rooms')
                                            ->flatten()
                                            ->pluck('room_category_id')
                                            ->unique()
                                            ->filter();
                                        return \App\Models\Base\RoomCategory::whereIn('id', $roomCategoryIds)
                                            ->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Select room type')
                                    ->required(fn ($get) => $get('is_stay_same_hotel'))
                                    ->visible(fn ($get) => $get('is_stay_same_hotel'))
                                    ->columnSpan(1),

                                Select::make('living_city_id')
                                    ->label('Living City')
                                    ->options(function () {
                                        $tenantSetting = \App\Models\TenantSetting::where('tenant_id', tenant('id'))->first();
                                        if (!$tenantSetting || !$tenantSetting->country_id) {
                                            return \App\Models\Base\City::all()->pluck('name', 'id');
                                        }
                                        return \App\Models\Base\City::whereHas('province', function ($query) use ($tenantSetting) {
                                            $query->where('country_id', $tenantSetting->country_id);
                                        })->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(2),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add Companion')
                            ->defaultItems(0)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => 
                                $state['companion_type_id'] ? 
                                \App\Models\Tenants\CompanionType::find($state['companion_type_id'])?->name : 
                                'New Companion'
                            ),
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ])
            ->fillForm(function ($record) {
                return [
                    'is_include_driver_meal' => $record->is_include_driver_meal,
                    'is_include_driver_hotel' => $record->is_include_driver_hotel,
                    'is_driver_stay_same_hotel' => $record->is_driver_stay_same_hotel,
                    'is_driver_same_meal' => $record->is_driver_same_meal,
                    'driver_room_category_id' => $record->driver_room_category_id,
                    'companions' => $record->quotationOfferGroupCompanions->map(function ($companion) {
                        return [
                            'companion_type_id' => $companion->companion_type_id,
                            'is_same_meal' => $companion->is_same_meal,
                            'is_stay_same_hotel' => $companion->is_stay_same_hotel,
                            'room_category_id' => $companion->room_category_id,
                            'living_city_id' => $companion->living_city_id,
                        ];
                    })->toArray(),
                ];
            })
            ->action(function (array $data, $record) {
                $record->update([
                    'is_include_driver_meal' => $data['is_include_driver_meal'] ?? false,
                    'is_include_driver_hotel' => $data['is_include_driver_hotel'] ?? false,
                    'is_driver_stay_same_hotel' => $data['is_driver_stay_same_hotel'] ?? false,
                    'is_driver_same_meal' => $data['is_driver_same_meal'] ?? false,
                    'driver_room_category_id' => $data['driver_room_category_id'] ?? null,
                ]);

                // Update companions
                if (isset($data['companions']) && is_array($data['companions'])) {
                    // Delete existing companions
                    $record->quotationOfferGroupCompanions()->delete();
                    
                    // Create new companions
                    foreach ($data['companions'] as $companionData) {
                        if (!empty($companionData['companion_type_id'])) {
                            $record->quotationOfferGroupCompanions()->create([
                                'companion_type_id' => $companionData['companion_type_id'],
                                'is_stay_same_hotel' => $companionData['is_stay_same_hotel'] ?? false,
                                'is_same_meal' => $companionData['is_same_meal'] ?? false,
                                'room_category_id' => $companionData['room_category_id'] ?? null,
                                'living_city_id' => $companionData['living_city_id'] ?? null,
                            ]);
                        }
                    }
                }

                Notification::make()->title('Updated!')->success()->send();
            })
            ->modalHeading('Edit Offer Group');
    }

    private static function deleteOfferGroupAction(): Action
    {
        return Action::make('delete_offer_group')
            ->label('Delete')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->size('sm')
            ->requiresConfirmation()
            ->action(function ($record) {
                $record->delete();
                Notification::make()->title('Deleted!')->success()->send();
            });
    }

    private static function driverInfoGrid(): Grid
    {
        return Grid::make(5)
            ->schema([
                // 1. Driver Meal Cost
                IconEntry::make('is_include_driver_meal')
                    ->label('Driver Meal Cost')
                    ->boolean()
                    ->icon(fn($state) => 'heroicon-o-check-circle')
                    ->color(fn($state) => $state ? 'success' : 'gray'),

                // 2. Same Meal
                IconEntry::make('is_driver_same_meal')
                    ->label('Same Meal')
                    ->boolean()
                    ->icon(fn($state) => 'heroicon-o-check-circle')
                    ->color(fn($state) => $state ? 'success' : 'gray')
                    ->hidden(fn($record) => !$record->is_include_driver_meal),

                // 3. Driver Hotel
                IconEntry::make('is_include_driver_hotel')
                    ->label('Driver Hotel Cost')
                    ->boolean()
                    ->icon(fn($state) => 'heroicon-o-check-circle')
                    ->color(fn($state) => $state ? 'success' : 'gray'),

                // 4. Same Hotel
                IconEntry::make('is_driver_stay_same_hotel')
                    ->label('Same Hotel')
                    ->boolean()
                    ->icon(fn($state) => 'heroicon-o-check-circle')
                    ->color(fn($state) => $state ? 'success' : 'gray'),

                // 5. Room Category
                TextEntry::make('driver_room_category_id')
                    ->label('Driver Room')
                    ->formatStateUsing(fn($state, $record) => 
                        $record->driverRoomCategory?->name ?? 'Not specified'
                    )
                    ->icon('heroicon-o-home')
                    ->color('primary')
                    ->hidden(fn($record) => !$record->is_driver_stay_same_hotel),
            ]);
    }

    private static function companionsTable(): RepeatableEntry
    {
        return RepeatableEntry::make('quotationOfferGroupCompanions')
            ->label('Companions')
            ->schema([
                Grid::make(5)
                    ->schema([
                        TextEntry::make('companionType.name')
                            ->label('Companion Type')
                            ->icon('heroicon-o-user')
                            ->color('primary'),
                            IconEntry::make('is_same_meal')
                            ->label('Same Meal')
                            ->boolean()
                            ->icon(fn($state) => 'heroicon-o-check-circle')
                            ->color(fn($state) => $state ? 'success' : 'gray'),
                            
                        IconEntry::make('is_stay_same_hotel')
                            ->label('Same Hotel')
                            ->boolean()
                            ->icon(fn($state) => 'heroicon-o-check-circle')
                            ->color(fn($state) => $state ? 'success' : 'gray'),

                        TextEntry::make('roomCategory.name')
                            ->label('Room Type')
                            ->formatStateUsing(fn($state) => $state ?? 'N/A')
                            ->icon('heroicon-o-home')
                            ->color('primary')
                            ->hidden(fn($record) => !$record->is_stay_same_hotel),

                        TextEntry::make('livingCity.name')
                            ->label('Living City')
                            ->icon('heroicon-o-map-pin')
                            ->color('success'),
                    ])
            ])
            ->columns(1);
    }

    private static function offersList(): Section
    {
        return Section::make('Offers')
            ->description('Manage offers for this offer group')
            ->icon('heroicon-o-ticket')
            ->schema([
                self::noOffersMessageForOffers(),
                self::offersTable(),
            ])
            ->collapsible()
            ->collapsed(false);
    }

    private static function noOffersMessageForOffers(): TextEntry
    {
        return TextEntry::make('id')
            ->hiddenLabel()
            ->label('')
            ->formatStateUsing(fn() => 'No offers have been created yet.')
            ->icon('heroicon-o-information-circle')
            ->color('gray')
            ->hidden(fn($record) => $record->quotationOffers()->count() > 0);
    }

    private static function offersTable(): RepeatableEntry
    {
        return RepeatableEntry::make('quotationOffers')
            ->contained(false)
            ->hiddenLabel()
            ->label('')
            ->hidden(fn($record) => $record->quotationOffers()->count() === 0)
            ->schema([
                self::offerDetailsGrid(),
            ])
            ->columns(1);
    }

    private static function offerDetailsGrid(): Grid
    {
        return Grid::make(1)
            ->schema([
                Grid::make(8)
                    ->schema([
                        TextEntry::make('vehicleType.name')
                            ->label('Vehicle Type')
                            ->icon('heroicon-o-truck')
                            ->color('primary')
                            ->formatStateUsing(fn($state) => $state ?? 'N/A'),

                        TextEntry::make('pax_qty')
                            ->label('PAX Qty')
                            ->icon('heroicon-o-users')
                            ->color('success')
                            ->formatStateUsing(fn($state) => $state ?? 0),

                        TextEntry::make('leaders_qty')
                            ->label('Leaders Qty')
                            ->icon('heroicon-o-user-group')
                            ->color('warning')
                            ->formatStateUsing(fn($state) => $state ?? 0),

                        TextEntry::make('drivers_qty')
                            ->label('Drivers Qty')
                            ->icon('heroicon-o-user')
                            ->color('info')
                            ->formatStateUsing(fn($state) => $state ?? 0),

                        TextEntry::make('markup')
                            ->label('Markup')
                            ->formatStateUsing(fn($state) => ($state ?? 0) . '%')
                            ->icon('heroicon-o-calculator')
                            ->color('warning'),

                        TextEntry::make('id')
                            ->label('Edit')
                            ->formatStateUsing(fn() => '')
                            ->icon('heroicon-m-pencil-square')
                            ->color('primary')
                            ->action(self::editOfferAction()),

                        TextEntry::make('id')
                            ->label('Report')
                            ->formatStateUsing(fn() => '')
                            ->icon('heroicon-m-document-text')
                            ->color('success')
                            ->action(self::viewReportAction()),

                        TextEntry::make('id')
                            ->label('Delete')
                            ->formatStateUsing(fn() => '')
                            ->icon('heroicon-m-trash')
                            ->color('danger')
                            ->action(self::deleteOfferAction()),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    private static function editOfferAction(): Action
    {
        return Action::make('edit_offer')
            ->label('Edit')
            ->icon('heroicon-m-pencil-square')
            ->color('primary')
            ->size('sm')
            ->action(function ($record) {
                // TODO: Implement edit offer logic
                Notification::make()
                    ->title('Edit Offer')
                    ->body('Edit offer functionality will be implemented soon.')
                    ->info()
                    ->send();
            });
    }

    private static function viewReportAction(): Action
    {
        return Action::make('view_report')
            ->label('View Report')
            ->icon('heroicon-m-document-text')
            ->color('success')
            ->modalHeading('Offer Details Report')
            ->modalWidth('7xl')
            ->modalContent(function ($record) {
                // Eager load all necessary relationships
                $offer = \App\Models\Tenants\QuotationOffer::with([
                    'vehicleType',
                    'quotationOfferGroup.quotationOfferGroupAttractions.attraction',
                    'quotationOfferGroup.quotationOfferGroupAttractions.subAttractions.subAttraction',
                    'quotationOfferGroup.quotationOfferGroupAttractions.subAttractions.quotationOfferGroupAttraction.attraction',
                    'quotationOfferGroup.quotationOfferGroupMeals.mealType',
                    'quotationOfferGroup.quotationOfferGroupTickets.fromCity',
                    'quotationOfferGroup.quotationOfferGroupTickets.toCity',
                    'quotationOfferGroup.quotationOfferGroupExpenses',
                    'quotationOfferGroup.quotationOfferGroupExperiences.experience.city',
                    'quotationOfferGroup.quotationItinerary.breakdown.attractions.attraction',
                    'quotationOfferGroup.quotationItinerary.breakdown.attractions.city',
                    'quotationOfferGroup.quotationItinerary.breakdown.currency',
                    'quotationOfferGroup.quotationItinerary.quotation.currency',
                    'quotationOfferDriverMeals.mealType',
                    'quotationOfferDriverAccommodations.accommodation',
                    'quotationOfferDriverAccommodations.city',
                    'quotationOfferLeaderMeals.mealType',
                    'quotationOfferLeaderAttractions.attraction',
                    'quotationOfferLeaderAttractions.subAttractions.subAttraction',
                    'quotationOfferLeaderTickets.fromCity',
                    'quotationOfferLeaderTickets.toCity',
                    'quotationOfferLeaderExperiences.experience.city',
                    'quotationOfferLeaderExpenses',
                    'quotationOfferLeaderAccommodations.accommodation',
                    'quotationOfferGroup.quotationOfferGroupCompanions.companionType',
                    'quotationOfferGroup.quotationOfferGroupCompanions.livingCity',
                    'quotationOfferGroup.quotationOfferGroupCompanions.meals.mealType',
                    'quotationOfferGroup.quotationOfferGroupCompanions.accommodations.accommodation',
                    'quotationOfferGroup.quotationOfferGroupCompanions.accommodations.city',
                    'quotationOfferGroup.quotationOfferGroupCompanions.attractions.attraction',
                    'quotationOfferGroup.quotationOfferGroupCompanions.attractions.subAttractions.subAttraction',
                    'quotationOfferGroup.quotationOfferGroupCompanions.experiences.experience',
                    'quotationOfferGroup.quotationOfferGroupCompanions.expenses',
                    'quotationOfferGroup.quotationOfferGroupCompanions.tickets.fromCity',
                    'quotationOfferGroup.quotationOfferGroupCompanions.tickets.toCity',
                    'quotationOfferPrices.roomCategory',
                    'quotationOfferPrices.quotationOfferPriceAccommodations.accommodation',
                ])->find($record->id);
                
                return view('filament.app.pages.offer-report', ['offer' => $offer]);
            })
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close');
    }

    private static function deleteOfferAction(): Action
    {
        return Action::make('delete_offer')
            ->label('Delete')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->size('sm')
            ->requiresConfirmation()
            ->modalHeading('Delete Offer')
            ->modalDescription('Are you sure you want to delete this offer? This action cannot be undone.')
            ->modalSubmitActionLabel('Yes, Delete')
            ->action(function ($record) {
                try {
                    // Delete the offer - all related records will be automatically deleted due to CASCADE constraints
                    $record->delete();
                    $record->refresh();
                    Notification::make()
                        ->title('Offer Deleted Successfully!')
                        ->body('The offer and all related records have been automatically deleted.')
                        ->success()
                        ->send();

                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Error Deleting Offer')
                        ->body('An error occurred while deleting the offer: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    private static function createOfferAction(): Action
    {
        return Action::make('create_offer')
            ->label('Create Offer')
            ->icon('heroicon-o-plus-circle')
            ->color('success')
            ->size('sm')
            ->schema([
                Section::make('Offer Details')
                    ->description('Create a new offer for this offer group')
                    ->icon('heroicon-o-ticket')
                    ->schema([
                        Select::make('vehicle_type_id')
                            ->label('Vehicle Type')
                            ->options(function () {
                                return \App\Models\Tenants\VehicleType::all()->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->columnSpan(1),

                        TextInput::make('leaders_qty')
                            ->label('Leaders Quantity')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->columnSpan(1),

                        Select::make('leader_room_category_id')
                            ->label('Leader Room Category')
                            ->options(function ($record) {
                                $quotationItinerary = $record->quotationItinerary;
                                if (!$quotationItinerary || !$quotationItinerary->breakdown) return [];
                                
                                $roomCategoryIds = $quotationItinerary->breakdown
                                    ->accommodations()
                                    ->with('rooms')
                                    ->get()
                                    ->pluck('rooms')
                                    ->flatten()
                                    ->pluck('room_category_id')
                                    ->unique()
                                    ->filter();
                                return \App\Models\Base\RoomCategory::whereIn('id', $roomCategoryIds)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),

                        TextInput::make('pax_qty')
                            ->label('PAX Quantity')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('drivers_qty')
                            ->label('Drivers Quantity')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('markup')
                            ->label('Markup (%)')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false),
            ])
            ->action(function (array $data, $record) {
                try {
                    // Validate required fields
                    if (!isset($data['vehicle_type_id']) || empty($data['vehicle_type_id'])) {
                        Notification::make()
                            ->title('Validation Error')
                            ->body('Vehicle Type is required.')
                            ->danger()
                            ->send();
                        return;
                    }

                    \Illuminate\Support\Facades\DB::transaction(function () use ($data, $record) {
                        // Create the offer
                        $offer = $record->quotationOffers()->create([
                            'vehicle_type_id' => $data['vehicle_type_id'],
                            'leaders_qty' => $data['leaders_qty'] ?? 0,
                            'leader_room_category_id' => $data['leader_room_category_id'] ?? null,
                            'pax_qty' => $data['pax_qty'] ?? 1,
                            'drivers_qty' => $data['drivers_qty'] ?? 1,
                            'markup' => $data['markup'] ?? 0,
                        ]);

                        // Calculate vehicle pricing from breakdown
                        $offer->calculateVehiclePricing();

                        // Calculate driver meal costs
                        $offer->calculateDriverMealCosts();
                        
                        // Calculate driver accommodation costs
                        $offer->calculateDriverAccommodationCosts();
                        
                        // Calculate leader accommodation costs
                        $offer->calculateLeaderAccommodationCosts();
                        
                        // Calculate leader attractions costs
                        $offer->calculateLeaderAttractionsCosts();
                        
                        // Calculate leader expenses costs
                        $offer->calculateLeaderExpensesCosts();
                        
                        // Calculate leader experiences costs
                        $offer->calculateLeaderExperiencesCosts();
                        
                        // Calculate leader meals costs
                        $offer->calculateLeaderMealsCosts();
                        
                        // Calculate leader tickets costs
                        $offer->calculateLeaderTicketsCosts();
                        
                        // Calculate offer prices for all room categories
                        $offer->calculateOfferPrices();
                    });

                    Notification::make()
                        ->title('Offer Created Successfully!')
                        ->body('The offer has been created successfully. All costs including driver, leader, companions, and final prices for all room categories have been calculated.')
                        ->success()
                        ->send();

                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Error Creating Offer')
                        ->body('An error occurred while creating the offer: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            })
            ->modalHeading('Create New Offer')
            ->modalSubmitActionLabel('Create Offer');
    }
}
