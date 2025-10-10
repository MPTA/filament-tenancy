<?php

namespace App\Filament\Tenant\Resources\ExchangeRates\Schemas;

use App\Models\Base\Currency;
use App\Models\TenantSetting;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

class ExchangeRateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Exchange Rate Information')
                    ->description('Enter the exchange rate details')
                    ->schema([
                        Select::make('from_currency_id')
                            ->label('From Currency')
                            ->options(Currency::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->placeholder('Select source currency')
                            ->rules([
                                'required',
                                'different:to_currency_id',
                                function () {
                                    return function (string $attribute, $value, \Closure $fail) {
                                        $record = request()->route('record');
                                        
                                        // Get to_currency_id from tenant settings
                                        $tenantSetting = tenant()->settings;
                                        $toCurrencyId = $tenantSetting?->currency_id;
                                        
                                        if (!$toCurrencyId) {
                                            return;
                                        }
                                        
                                        // Check if from_currency is same as to_currency
                                        if ($value == $toCurrencyId) {
                                            $fail('From Currency and To Currency cannot be the same.');
                                            return;
                                        }
                                        
                                        // Check for unique combination
                                        $query = \App\Models\Tenants\ExchangeRate::where('from_currency_id', $value)
                                            ->where('to_currency_id', $toCurrencyId);
                                        
                                        if ($record) {
                                            $query->where('id', '!=', $record);
                                        }
                                        
                                        if ($query->exists()) {
                                            $fail('This exchange rate combination already exists.');
                                        }
                                    };
                                },
                            ]),
                        
                        Select::make('to_currency_id')
                            ->label('To Currency (Tenant Default)')
                            ->options(Currency::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->default(function () {
                                $tenantSetting = TenantSetting::first();
                                return $tenantSetting?->currency_id;
                            })
                            ->afterStateHydrated(function (Select $component, $state) {
                                if (!$state) {
                                    $tenantSetting = TenantSetting::first();
                                    $component->state($tenantSetting?->currency_id);
                                }
                            })
                            ->dehydrateStateUsing(function ($state) {
                                if (!$state) {
                                    $tenantSetting = TenantSetting::first();
                                    return $tenantSetting?->currency_id;
                                }
                                return $state;
                            })
                            ->disabled()
                            ->dehydrated()
                            ->helperText('This is automatically set to your tenant\'s default currency'),
                        
                        TextInput::make('rate')
                            ->label('Exchange Rate')
                            ->numeric()
                            ->required()
                            ->step(0.000001)
                            ->placeholder('e.g., 1.25')
                            ->helperText('Enter the exchange rate (1 from currency = X to currency)')
                            ->rules(['min:0.000001']),
                    ])
                    ->columns(2)
            ]);
    }
}
