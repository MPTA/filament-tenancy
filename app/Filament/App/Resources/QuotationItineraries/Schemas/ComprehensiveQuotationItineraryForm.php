<?php

namespace App\Filament\App\Resources\QuotationItineraries\Schemas;

use App\Enums\InquiryDateTypeEnum;
use App\Enums\InquiryTypeEnum;
use App\Enums\QuotationTypeEnum;
use App\Enums\StarRatingEnum;
use App\Models\Base\Currency;
use App\Models\Base\RoomCategory;
use App\Models\Tenants\TenantContact;
use App\Models\TenantSetting;
use App\Models\Tenants\ExchangeRate;
use App\Models\Contact;
use App\Enums\ContactTypeEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Facades\Auth;

class ComprehensiveQuotationItineraryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('app-quotation-itineraries.sections.inquiry_information.title'))
                    ->description(__('app-quotation-itineraries.sections.inquiry_information.description'))
                    ->schema([
                        TextInput::make('inquiry_title')
                            ->label(__('app-quotation-itineraries.fields.inquiry_title'))
                            ->required()
                            ->maxLength(255),
                        
                        FileUpload::make('inquiry_attachments')
                            ->label(__('app-quotation-itineraries.fields.attachments'))
                            ->multiple()
                            ->disk('local')
                            ->directory(fn () => TenantSetting::getTenantDirectory('inquiries'))
                            ->visibility('private')
                            ->downloadable()
                            ->openable()
                            ->deletable()
                            ->reorderable()
                            ->maxFiles(10)
                            ->maxSize(10240) // 10MB
                            ->helperText(__('app-quotation-itineraries.helpers.attachments'))
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'image/jpeg',
                                'image/png',
                                'image/gif',
                                'image/webp',
                            ]),
                        
                        Grid::make(3)
                            ->schema([
                                TextInput::make('inquiry_reference')
                                    ->label(__('app-quotation-itineraries.fields.reference'))
                                    ->maxLength(255),
                                
                                Select::make('inquiry_contact_id')
                                    ->label(__('app-quotation-itineraries.fields.contact'))
                                    ->options(fn () => TenantContact::query()
                                        ->whereIn('type', [ContactTypeEnum::LEAD, ContactTypeEnum::CUSTOMER])
                                        ->orderBy('first_name')
                                        ->orderBy('last_name')
                                        ->get()
                                        ->mapWithKeys(fn ($contact) => [$contact->id => $contact->full_name])
                                        ->toArray())
                                    ->getOptionLabelUsing(fn ($value) => TenantContact::find($value)?->full_name)
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->suffixActions([
                                        Action::make('quick_add_contact')
                                            ->label(__('app-quotation-itineraries.actions.quick_add'))
                                            ->icon('heroicon-o-user-plus')
                                            ->modalHeading(__('app-quotation-itineraries.actions.quick_add_contact'))
                                            ->schema([
                                                Grid::make(2)->schema([
                                                    TextInput::make('contact.first_name')
                                                        ->label(__('app-quotation-itineraries.fields.first_name'))
                                                        ->required()
                                                        ->maxLength(100),
                                                    TextInput::make('contact.last_name')
                                                        ->label(__('app-quotation-itineraries.fields.last_name'))
                                                        ->maxLength(100),
                                                ]),
                                                Grid::make(2)->schema([
                                                    TextInput::make('contact.email')
                                                        ->label(__('common-fields.email'))
                                                        ->email()
                                                        ->required()
                                                        ->maxLength(191),
                                                    TextInput::make('contact.mobile')
                                                        ->label(__('app-quotation-itineraries.fields.mobile'))
                                                        ->tel()
                                                        ->maxLength(50),
                                                ]),
                                                TextInput::make('contact.company')
                                                    ->label(__('common-fields.company'))
                                                    ->maxLength(191),
                                            ])
                                            ->action(function (array $data, Set $set) {
                                                $payload = $data['contact'] ?? [];
                                                if (empty($payload['first_name']) || empty($payload['email'])) {
                                                    Notification::make()
                                                        ->title(__('app-quotation-itineraries.notifications.contact_required_fields'))
                                                        ->danger()
                                                        ->send();
                                                    return;
                                                }

                                                $contact = Contact::create([
                                                    'first_name' => $payload['first_name'],
                                                    'last_name' => $payload['last_name'] ?? null,
                                                    'email' => $payload['email'],
                                                    'mobile' => $payload['mobile'] ?? null,
                                                    'company' => $payload['company'] ?? null,
                                                    'type' => ContactTypeEnum::LEAD,
                                                    'tenant_id' => tenant('id'),
                                                ]);

                                                $set('inquiry_contact_id', $contact->id);

                                                Notification::make()
                                                    ->title(__('app-quotation-itineraries.notifications.contact_added_title'))
                                                    ->body(__('app-quotation-itineraries.notifications.contact_added_body'))
                                                    ->success()
                                                    ->send();
                                            })
                                    ]),
                                
                                Select::make('inquiry_requested_currency_id')
                                    ->label(__('app-quotation-itineraries.fields.requested_currency'))
                                    ->options(fn () => Currency::pluck('name', 'id')->toArray())
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state) {
                                            $exchangeRate = self::calculateExchangeRate($state);
                                            $set('exchange_rate', $exchangeRate);
                                        }
                                    }),
                            ]),
                    ]),
                
                Section::make(__('app-quotation-itineraries.sections.inquiry_itinerary_details.title'))
                    ->description(__('app-quotation-itineraries.sections.inquiry_itinerary_details.description'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('inquiry_date_type')
                                    ->label(__('app-quotation-itineraries.fields.date_type'))
                                    ->options(InquiryDateTypeEnum::getOptions())
                                    ->required()
                                    ->reactive(),
                                
                                Select::make('accommodation_stars')
                                    ->label(__('app-quotation-itineraries.fields.accommodation_stars'))
                                    ->options(StarRatingEnum::getOptions()),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('from_date')
                                    ->label(fn ($get) => match($get('inquiry_date_type')) {
                                        InquiryDateTypeEnum::FIXED_DATE->value => __('app-quotation-itineraries.fields.arrival'),
                                        InquiryDateTypeEnum::SERIES->value => __('app-quotation-itineraries.fields.start_date'),
                                        InquiryDateTypeEnum::FLEXIBLE_DATE->value => __('app-quotation-itineraries.fields.from_date'),
                                        default => __('app-quotation-itineraries.fields.from_date'),
                                    })
                                    ->reactive()
                                    ->required()
                                    ->visible(fn ($get) => in_array($get('inquiry_date_type'), [
                                        InquiryDateTypeEnum::FIXED_DATE->value,
                                        InquiryDateTypeEnum::SERIES->value,
                                        InquiryDateTypeEnum::FLEXIBLE_DATE->value
                                    ]))
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        // Clear to_date if it's before or equal to from_date
                                        $toDate = $get('to_date');
                                        if ($toDate && $state && $toDate <= $state) {
                                            $set('to_date', null);
                                        }
                                    }),
                                
                                DatePicker::make('to_date')
                                    ->label(fn ($get) => match($get('inquiry_date_type')) {
                                        InquiryDateTypeEnum::FIXED_DATE->value => __('app-quotation-itineraries.fields.departure'),
                                        InquiryDateTypeEnum::SERIES->value => __('app-quotation-itineraries.fields.end_date'),
                                        InquiryDateTypeEnum::FLEXIBLE_DATE->value => __('app-quotation-itineraries.fields.to_date'),
                                        default => __('app-quotation-itineraries.fields.to_date'),
                                    })
                                    ->reactive()
                                    ->required()
                                    ->visible(fn ($get) => in_array($get('inquiry_date_type'), [
                                        InquiryDateTypeEnum::FIXED_DATE->value,
                                        InquiryDateTypeEnum::FLEXIBLE_DATE->value,
                                        InquiryDateTypeEnum::SERIES->value,
                                    ]))
                                    ->minDate(fn ($get) => $get('from_date'))
                                    ->disabled(fn ($get) => !$get('from_date'))
                                    ->helperText(fn ($get) => !$get('from_date') 
                                        ? __('app-quotation-itineraries.helpers.from_date_required')
                                        : __('app-quotation-itineraries.helpers.to_date_after_from'))
                                    ->rules([
                                        'required',
                                        function ($get) {
                                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                                $fromDate = $get('from_date');
                                                if ($fromDate && $value) {
                                                    $from = \Carbon\Carbon::parse($fromDate);
                                                    $to = \Carbon\Carbon::parse($value);
                                                    
                                                    if ($to->lt($from)) {
                                                        $fail(__('app-quotation-itineraries.validations.to_date_after_from_date'));
                                                    }
                                                }
                                            };
                                        },
                                    ]),
                            ]),
                    ]),
                
                Section::make(__('app-quotation-itineraries.sections.quotation_information.title'))
                    ->description(__('app-quotation-itineraries.sections.quotation_information.description'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('exchange_rate')
                                    ->label(__('app-quotation-itineraries.fields.exchange_rate'))
                                    ->placeholder(__('app-quotation-itineraries.placeholders.exchange_rate'))
                                    ->default('1.0000')
                                    ->required()
                                    ->live()
                                    ->helperText(function ($get) {
                                        $requestedCurrencyId = $get('inquiry_requested_currency_id');
                                        $exchangeRate = $get('exchange_rate');
                                        
                                        if ($requestedCurrencyId) {
                                            $requestedCurrency = Currency::find($requestedCurrencyId);
                                            $tenantSetting = tenant()->settings;
                                            $tenantCurrency = $tenantSetting?->currency;
                                            
                                            if ($requestedCurrency && $tenantCurrency) {
                                                $rateDisplay = ($exchangeRate && $exchangeRate > 0) ? $exchangeRate : '...';
                                                return __('app-quotation-itineraries.helpers.exchange_rate_display', [
                                                    'from' => $requestedCurrency->code,
                                                    'rate' => $rateDisplay,
                                                    'to' => $tenantCurrency->code
                                                ]);
                                            }
                                        }
                                        
                                        return __('app-quotation-itineraries.helpers.exchange_rate_format');
                                    })
                                    ->rules(['required', 'regex:/^\d+(\.\d{1,4})?$/', 'numeric', 'gt:0'])
                                    ->validationMessages([
                                        'regex' => __('app-quotation-itineraries.validations.exchange_rate_regex'),
                                        'numeric' => __('app-quotation-itineraries.validations.exchange_rate_numeric'),
                                        'gt' => __('app-quotation-itineraries.validations.exchange_rate_gt'),
                                    ]),
                                
                                DatePicker::make('expire_date')
                                    ->label(__('app-quotation-itineraries.fields.expire_date'))
                                    ->required()
                                    ->after('today'),
                            ]),
                        
                        // Room categories selection (required)
                        Select::make('room_category_ids')
                            ->label(__('app-quotation-itineraries.fields.room_categories'))
                            ->required()
                            ->multiple()
                            ->maxItems(3)
                            ->options(fn () => RoomCategory::where('is_active', true)
                                ->get()
                                ->mapWithKeys(fn ($cat) => [$cat->id => $cat->category->getDisplayName()])
                                ->toArray()
                            )
                            ->default(function () {
                                $twin = RoomCategory::where('category', 'twin')->value('id');
                                $single = RoomCategory::where('category', 'single')->value('id');
                                return array_values(array_filter([$twin, $single]));
                            })
                            ->searchable()
                            ->preload()
                            ->helperText(__('app-quotation-itineraries.helpers.room_categories')),

                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_foreigner_passengers')
                                    ->label(__('app-quotation-itineraries.fields.foreigner_passengers'))
                                    ->helperText(__('app-quotation-itineraries.helpers.foreigner_passengers'))
                                    ->default(false),
                                
                                // Placeholder for future fields
                                Hidden::make('placeholder')
                                    ->default(''),
                            ]),
                        
                        Textarea::make('quotation_description')
                            ->label(__('app-quotation-itineraries.fields.description'))
                            ->rows(3)
                            ->helperText(__('app-quotation-itineraries.helpers.description_visible')),
                        
                        Textarea::make('internal_note')
                            ->label(__('app-quotation-itineraries.fields.internal_note'))
                            ->rows(2)
                            ->helperText(__('app-quotation-itineraries.helpers.internal_note_private')),
                    ]),
                
            ]);
    }

    /**
     * Calculate exchange rate based on tenant currency and requested currency.
     * 
     * Logic:
     * - If tenant has CNY and wants USD, we need rate from CNY to USD
     * - If tenant has USD and wants CNY, we need rate from USD to CNY
     * - Rate should be: 1 tenant_currency = X requested_currency
     */
    private static function calculateExchangeRate($requestedCurrencyId)
    {
        try {
            // Get tenant's currency from settings (BelongsToTenant handles tenant_id automatically)
            $tenantSetting = TenantSetting::first();
            $tenantCurrencyId = $tenantSetting?->currency_id;
            
            // If no tenant currency or same as requested, return 1
            if ($tenantCurrencyId === $requestedCurrencyId) {
                return 1.0000;
            }

            // Try to find exchange rate from tenant currency to requested currency
            // Example: If tenant has CNY and wants USD, look for CNY->USD rate
            $exchangeRate = ExchangeRate::where('from_currency_id', $tenantCurrencyId)
                ->where('to_currency_id', $requestedCurrencyId)
                ->first();

            if ($exchangeRate) {
                // Direct rate found: 1 tenant_currency = X requested_currency
                return (float) $exchangeRate->rate;
            }

            // If no direct rate found, try reverse rate
            // Example: If tenant has CNY and wants USD, but only USD->CNY rate exists
            $reverseRate = ExchangeRate::where('from_currency_id', $requestedCurrencyId)
                ->where('to_currency_id', $tenantCurrencyId)
                ->first();

            if ($reverseRate) {
                // Reverse rate found: 1 requested_currency = X tenant_currency
                // For display purposes, we want to show the rate as is
                // So if USD->CNY is 7, we show 7 (meaning 1 USD = 7 CNY)
                return (float) $reverseRate->rate;
            }

            // If no exchange rate found, return null
            return null;

        } catch (\Exception $e) {
            // If any error occurs, return null
            return null;
        }
    }
}
