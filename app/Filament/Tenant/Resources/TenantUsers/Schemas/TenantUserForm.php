<?php

namespace App\Filament\Tenant\Resources\TenantUsers\Schemas;

use App\Enums\GenderEnum;
use App\Models\Base\Country;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

class TenantUserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Essential Information')
                    ->description('Required information to create a user account')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpan(2),
                        
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->rules([Password::default()])
                            ->required(fn ($context) => $context === 'create')
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->dehydrateStateUsing(fn ($state): string => bcrypt($state))
                            ->helperText(fn ($context) => $context === 'edit' ? 'Leave blank to keep current password' : null)
                            ->columnSpan(1),
                        
                        TextInput::make('password_confirmation')
                            ->label('Confirm Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->required(fn ($context) => $context === 'create')
                            ->requiredWith('password')
                            ->same('password')
                            ->columnSpan(1),
                    ]),

                Section::make('Contact Information')
                    ->description('Contact details for this user')
                    ->columns(3)
                    ->schema([
                        TextInput::make('contact.phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(255)
                            ->columnSpan(1),
                        
                        TextInput::make('contact.mobile')
                            ->label('Mobile')
                            ->tel()
                            ->maxLength(255)
                            ->columnSpan(1),
                        
                        Select::make('contact.country_id')
                            ->label('Country')
                            ->options(fn () => Country::all()->pluck('name', 'id')->mapWithKeys(fn ($name, $id) => [$id => is_array($name) ? ($name['en'] ?? $name['fa'] ?? current($name)) : $name]))
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),
                    ]),

                Section::make('Additional Information')
                    ->description('Optional information about the user')
                    ->columns(2)
                    ->schema([
                        TextInput::make('contact.company')
                            ->label('Company')
                            ->maxLength(255)
                            ->columnSpan(1),
                        
                        Select::make('contact.gender')
                            ->label('Gender')
                            ->options(GenderEnum::class)
                            ->columnSpan(1),
                        
                        Textarea::make('contact.postal_address')
                            ->label('Address')
                            ->rows(3)
                            ->columnSpan(2),
                    ]),
            ]);
    }
}
