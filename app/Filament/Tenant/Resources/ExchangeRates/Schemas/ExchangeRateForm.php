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
                Section::make(__('tenant-exchange-rates.sections.exchange_rate_information.title'))
                    ->description(__('tenant-exchange-rates.sections.exchange_rate_information.description'))
                    ->schema([
                        Select::make('from_currency_id')
                            ->label(__('common-fields.from_currency'))
                            ->options(Currency::all()->mapWithKeys(fn($currency) => [$currency->id => "{$currency->name} ({$currency->code})"]))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->placeholder(__('tenant-exchange-rates.placeholders.from_currency'))
                            ->rules([
                                'required',
                                'different:to_currency_id',
                                function ($get, $livewire) {
                                    return function (string $attribute, $value, \Closure $fail) use ($get, $livewire) {
                                        // Get current record if in edit mode
                                        $record = $livewire->record ?? null;
                                        
                                        // Get to_currency_id from tenant settings
                                        $tenantSetting = tenant()->settings;
                                        $toCurrencyId = $tenantSetting?->currency_id;
                                        
                                        if (!$toCurrencyId) {
                                            return;
                                        }
                                        
                                        // Check if from_currency is same as to_currency
                                        if ($value == $toCurrencyId) {
                                            $fail(__('tenant-exchange-rates.validations.same_currency'));
                                            return;
                                        }
                                        
                                        // Check for unique combination
                                        $query = \App\Models\Tenants\ExchangeRate::where('from_currency_id', $value)
                                            ->where('to_currency_id', $toCurrencyId);
                                        
                                        if ($record) {
                                            // Exclude current record in edit mode
                                            $query->where('id', '!=', $record->id);
                                        }
                                        
                                        if ($query->exists()) {
                                            $fail(__('tenant-exchange-rates.validations.duplicate_combination'));
                                        }
                                    };
                                },
                            ]),
                        
                        Select::make('to_currency_id')
                            ->label(__('tenant-exchange-rates.fields.to_currency_tenant_default'))
                            ->options(Currency::all()->mapWithKeys(fn($currency) => [$currency->id => "{$currency->name} ({$currency->code})"]))
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
                            ->helperText(__('tenant-exchange-rates.helpers.to_currency_auto')),
                        
                        TextInput::make('rate')
                            ->label(__('common-fields.exchange_rate'))
                            ->numeric()
                            ->required()
                            ->step(0.000001)
                            ->placeholder(__('tenant-exchange-rates.placeholders.rate'))
                            ->helperText(__('tenant-exchange-rates.helpers.rate'))
                            ->rules(['min:0.000001']),
                    ])
                    ->columns(2)
            ]);
    }
}
