<?php

namespace App\Filament\App\Resources\QuotationItineraries\Schemas;

use App\Enums\InquiryDateTypeEnum;
use App\Enums\InquiryTypeEnum;
use App\Enums\QuotationTypeEnum;
use App\Models\Base\Currency;
use App\Models\Tenants\TenantContact;
use App\Models\TenantSetting;
use App\Models\Tenants\ExchangeRate;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ComprehensiveQuotationItineraryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Inquiry Information')
                    ->description('Basic inquiry details')
                    ->schema([
                        TextInput::make('inquiry_title')
                            ->label('Inquiry Title')
                            ->required()
                            ->maxLength(255),
                        
                        Textarea::make('inquiry_description')
                            ->label('Inquiry Description')
                            ->rows(3),
                        
                        Grid::make(3)
                            ->schema([
                                TextInput::make('inquiry_reference')
                                    ->label('Reference')
                                    ->maxLength(255),
                                
                                Select::make('inquiry_contact_id')
                                    ->label('Contact')
                                    ->options(fn () => TenantContact::query()
                                        ->get()
                                        ->mapWithKeys(fn ($contact) => [$contact->id => $contact->full_name])
                                        ->toArray())
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                
                                Select::make('inquiry_requested_currency_id')
                                    ->label('Requested Currency')
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
                
                Section::make('Inquiry Itinerary Details')
                    ->description('Travel itinerary information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('inquiry_date_type')
                                    ->label('Date Type')
                                    ->options(InquiryDateTypeEnum::getOptions())
                                    ->required()
                                    ->reactive(),
                                
                                Select::make('accommodation_stars')
                                    ->label('Accommodation Stars')
                                    ->options([
                                        1 => '1 Star',
                                        2 => '2 Stars',
                                        3 => '3 Stars',
                                        4 => '4 Stars',
                                        5 => '5 Stars',
                                    ]),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('from_date')
                                    ->label('From Date')
                                    ->reactive()
                                    ->required()
                                    ->visible(fn ($get) => in_array($get('inquiry_date_type'), [
                                        InquiryDateTypeEnum::FIXED_DATE->value,
                                        InquiryDateTypeEnum::FLEXIBLE_DATE->value
                                    ])),
                                
                                DatePicker::make('to_date')
                                    ->label('To Date')
                                    ->reactive()
                                    ->required()
                                    ->visible(fn ($get) => in_array($get('inquiry_date_type'), [
                                        InquiryDateTypeEnum::FIXED_DATE->value,
                                        InquiryDateTypeEnum::FLEXIBLE_DATE->value,
                                        InquiryDateTypeEnum::SERIES->value,
                                    ]))
                                    ->after('from_date'),
                            ]),
                    ]),
                
                Section::make('Quotation Information')
                    ->description('Quotation details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('exchange_rate')
                                    ->label('Exchange Rate')
                                    ->helperText(function ($get) {
                                        $requestedCurrencyId = $get('inquiry_requested_currency_id');
                                        $exchangeRate = $get('exchange_rate');
                                        
                                        if ($requestedCurrencyId) {
                                            $requestedCurrency = Currency::find($requestedCurrencyId);
                                            $tenantSetting = TenantSetting::first();
                                            $tenantCurrency = $tenantSetting?->currency;
                                            
                                            if ($requestedCurrency && $tenantCurrency) {
                                                if ($exchangeRate && $exchangeRate > 0) {
                                                    return "1 {$requestedCurrency->code} = {$exchangeRate} {$tenantCurrency->code}";
                                                } else {
                                                    return "1 {$requestedCurrency->code} = ... {$tenantCurrency->code}";
                                                }
                                            }
                                        }
                                        
                                        if ($exchangeRate && $exchangeRate > 0) {
                                            return "1 Quotation Currency = {$exchangeRate} Your Setting Currency";
                                        }
                                        
                                        return "1 Quotation Currency = ... Your Currency";
                                    })
                                    ->numeric()
                                    ->step(0.0001)
                                    ->default(1.0000)
                                    ->required()
                                    ->reactive(),
                                
                                DatePicker::make('expire_date')
                                    ->label('Expire Date')
                                    ->required()
                                    ->after('today'),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_foreigner_passengers')
                                    ->label('Foreigner Passengers')
                                    ->helperText('Check if this quotation is for foreign passengers')
                                    ->default(false),
                                
                                // Placeholder for future fields
                                Hidden::make('placeholder')
                                    ->default(''),
                            ]),
                        
                        Textarea::make('quotation_description')
                            ->label('Description')
                            ->rows(3),
                        
                        Textarea::make('internal_note')
                            ->label('Internal Note')
                            ->rows(2),
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
