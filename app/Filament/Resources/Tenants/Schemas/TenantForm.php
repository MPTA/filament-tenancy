<?php

namespace App\Filament\Resources\Tenants\Schemas;

use App\Models\Base\City;
use App\Models\Base\Country;
use App\Models\Base\Currency;
use App\Models\Base\Language;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Basic Information')
                    ->columns(3)
                    ->schema([
                        TextInput::make('name')
                            ->label('Tenant Name')
                            ->required()
                            ->unique(table: 'tenants', ignoreRecord: true)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, $state) {
                                $set('id', Str::slug($state, '_'));
                                $set('domain', Str::slug($state));
                            })
                            ->columnSpan(3),
                        
                        TextInput::make('domain')
                            ->label('Domain')
                            ->required()
                            ->visible(fn ($context) => $context === 'create')
                            ->unique(table: 'domains', ignoreRecord: true)
                            ->prefix(request()->getScheme() . '://')
                            ->suffix('.' . request()->getHost())
                            ->columnSpan(3),
                        
                        TextInput::make('email')
                            ->label('Email')
                            ->required()
                            ->email()
                            ->columnSpan(2),
                        
                        TextInput::make('phone')
                            ->label('Phone')
                            ->tel()
                            ->columnSpan(1),
                        
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false)
                            ->columnSpan(3),
                    ]),

                Section::make('Security')
                    ->visible(fn ($context) => $context === 'create')
                    ->columns(2)
                    ->schema([
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->rules([Password::default()])
                            ->autocomplete('new-password')
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->dehydrateStateUsing(fn ($state): string => bcrypt($state))
                            ->live(debounce: 500)
                            ->same('passwordConfirmation')
                            ->required(fn ($context) => $context === 'create'),
                        
                        TextInput::make('passwordConfirmation')
                            ->label('Confirm Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->required(fn ($context) => $context === 'create'),
                    ]),

                Section::make('Company & Settings')
                    ->columns(3)
                    ->schema([
                        TextInput::make('settings.company_name')
                            ->label('Company Name')
                            ->maxLength(255)
                            ->columnSpan(2),
                        
                        TextInput::make('settings.company_local_name')
                            ->label('Local Name')
                            ->maxLength(255)
                            ->columnSpan(1),
                        
                        Select::make('settings.currency_id')
                            ->label('Default Currency')
                            ->options(fn () => Currency::all()->mapWithKeys(fn ($currency) => [$currency->id => $currency->code]))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),
                        
                        Select::make('settings.language_id')
                            ->label('Default Language')
                            ->options(fn () => Language::all()->mapWithKeys(fn ($language) => [$language->id => $language->name]))
                            ->searchable()
                            ->preload()
                            ->columnSpan(2),
                    ]),

                Section::make('Location & Contact Details')
                    ->columns(3)
                    ->schema([
                        Select::make('settings.country_id')
                            ->label('Country')
                            ->options(fn () => Country::all()->pluck('name', 'id')->mapWithKeys(fn ($name, $id) => [$id => is_array($name) ? ($name['en'] ?? $name['fa'] ?? current($name)) : $name]))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('settings.city_id', null))
                            ->columnSpan(1),
                        
                        Select::make('settings.city_id')
                            ->label('City')
                            ->options(fn (Get $get) => 
                                City::query()
                                    ->when($get('settings.country_id'), fn ($query, $countryId) => 
                                        $query->whereHas('province', fn ($q) => $q->where('country_id', $countryId))
                                    )
                                    ->get()
                                    ->pluck('name', 'id')
                                    ->mapWithKeys(fn ($name, $id) => [$id => is_array($name) ? ($name['en'] ?? $name['fa'] ?? current($name)) : $name])
                            )
                            ->searchable()
                            ->preload()
                            ->disabled(fn (Get $get) => empty($get('settings.country_id')))
                            ->columnSpan(2),
                        
                        TextInput::make('settings.contact_name')
                            ->label('Contact Person')
                            ->maxLength(255)
                            ->columnSpan(1),
                        
                        TextInput::make('settings.phone_number')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(255)
                            ->columnSpan(1),
                        
                        TextInput::make('settings.mobile_number')
                            ->label('Mobile')
                            ->tel()
                            ->maxLength(255)
                            ->columnSpan(1),
                        
                        Textarea::make('settings.address')
                            ->label('Address')
                            ->rows(2)
                            ->columnSpan(3),
                    ]),
            ]);
    }
}
