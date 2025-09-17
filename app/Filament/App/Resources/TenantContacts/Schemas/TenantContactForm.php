<?php

namespace App\Filament\App\Resources\TenantContacts\Schemas;

use App\Enums\ContactTypeEnum;
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
                        Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),
                        
                        Select::make('type')
                            ->label('Contact Type')
                            ->options(ContactTypeEnum::class)
                            ->default('lead')
                            ->required(),
                        
                        TextInput::make('gender')
                            ->label('Gender')
                            ->maxLength(20),
                        
                        Toggle::make('is_customer')
                            ->label('Is Customer')
                            ->required(),
                        
                        TextInput::make('tenant_id')
                            ->label('Tenant ID')
                            ->maxLength(255),
                        
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
