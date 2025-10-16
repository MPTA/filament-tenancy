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
                Section::make(__('tenant-users.sections.essential_information.title'))
                    ->description(__('tenant-users.sections.essential_information.description'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('common-fields.full_name'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),
                        
                        Select::make('contact.gender')
                            ->label(__('common-fields.gender'))
                            ->options(GenderEnum::class)
                            ->columnSpan(1),
                        
                        TextInput::make('email')
                            ->label(__('common-fields.email_address'))
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpan(2),
                        
                        TextInput::make('password')
                            ->label(__('common-fields.password'))
                            ->password()
                            ->revealable()
                            ->rules([Password::default()])
                            ->required(fn ($context) => $context === 'create')
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->dehydrateStateUsing(fn ($state): string => bcrypt($state))
                            ->helperText(fn ($context) => $context === 'edit' ? __('tenant-users.messages.password_helper') : null)
                            ->columnSpan(1),
                        
                        TextInput::make('password_confirmation')
                            ->label(__('common-fields.confirm_password'))
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->required(fn ($context) => $context === 'create')
                            ->requiredWith('password')
                            ->same('password')
                            ->columnSpan(1),
                    ]),

                Section::make(__('tenant-users.sections.contact_information.title'))
                    ->description(__('tenant-users.sections.contact_information.description'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('contact.phone')
                            ->label(__('common-fields.phone'))
                            ->tel()
                            ->maxLength(255)
                            ->columnSpan(1),
                        
                        TextInput::make('contact.mobile')
                            ->label(__('common-fields.mobile'))
                            ->tel()
                            ->maxLength(255)
                            ->columnSpan(1),
                        
                        Select::make('contact.country_id')
                            ->label(__('common-fields.country'))
                            ->options(fn () => Country::all()->pluck('name', 'id')->mapWithKeys(fn ($name, $id) => [$id => is_array($name) ? ($name['en'] ?? $name['fa'] ?? current($name)) : $name]))
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),
                        
                        TextInput::make('contact.company')
                            ->label(__('common-fields.company'))
                            ->maxLength(255)
                            ->columnSpan(1),
                        
                        Textarea::make('contact.postal_address')
                            ->label(__('common-fields.address'))
                            ->rows(3)
                            ->columnSpan(2),
                    ]),
            ]);
    }
}
