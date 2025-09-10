<?php

namespace App\Filament\Tenant\Resources\TenantContacts\Schemas;

use App\Enums\ContactTypeEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TenantContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name'),
                Textarea::make('postal_address')
                    ->columnSpanFull(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('mobile'),
                TextInput::make('country_code'),
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('last_name'),
                TextInput::make('gender'),
                Toggle::make('is_customer')
                    ->required(),
                TextInput::make('tenant_id'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('company'),
                        Select::make('type')
                            ->options(ContactTypeEnum::class)
                    ->default('lead')
                    ->required(),
            ]);
    }
}
