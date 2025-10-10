<?php

namespace App\Filament\App\Resources\TenantContacts\Schemas;

use App\Enums\GenderEnum;
use App\Models\Base\Country;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TenantContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contact Information')
                    ->description('Enter the contact details')
                    ->schema([
                        TextInput::make('first_name')
                            ->label('First Name')
                            ->required()
                            ->maxLength(255),
                        
                        TextInput::make('last_name')
                            ->label('Last Name')
                            ->maxLength(255),
                        
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        
                        TextInput::make('phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(20),
                        
                        TextInput::make('mobile')
                            ->label('Mobile')
                            ->tel()
                            ->maxLength(20),
                        
                        TextInput::make('country_code')
                            ->label('Country Code')
                            ->maxLength(10),
                        
                        TextInput::make('company')
                            ->label('Company')
                            ->maxLength(255),
                    ])
                    ->columns(2),
                
                Section::make('Additional Information')
                    ->schema([
                        Select::make('gender')
                            ->label('Gender')
                            ->options(GenderEnum::class)
                            ->searchable(),
                        
                        Select::make('country_id')
                            ->label('Country')
                            ->required()
                            ->options(fn () => Country::all()->pluck('name', 'id')->mapWithKeys(fn ($name, $id) => [$id => is_array($name) ? ($name['en'] ?? $name['fa'] ?? current($name)) : $name]))
                            ->searchable()
                            ->preload(),
                        
                        Toggle::make('is_customer')
                            ->label('Mark as Customer')
                            ->helperText('Enable to save this contact as a customer (otherwise saved as lead)')
                            ->default(false)
                            ->inline(false)
                            ->columnSpanFull(),
                        
                        Textarea::make('postal_address')
                            ->label('Postal Address')
                            ->columnSpanFull()
                            ->rows(3),
                    ])
                    ->columns(2)
                    ->collapsible()
            ]);
    }
}
