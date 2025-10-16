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
                Section::make(__('app-contacts.sections.contact_information.title'))
                    ->description(__('app-contacts.sections.contact_information.description'))
                    ->schema([
                        TextInput::make('first_name')
                            ->label(__('app-contacts.fields.first_name'))
                            ->required()
                            ->maxLength(255),
                        
                        TextInput::make('last_name')
                            ->label(__('app-contacts.fields.last_name'))
                            ->maxLength(255),
                        
                        TextInput::make('email')
                            ->label(__('app-contacts.fields.email_address'))
                            ->email()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        
                        TextInput::make('phone')
                            ->label(__('common-fields.phone'))
                            ->tel()
                            ->maxLength(20),
                        
                        TextInput::make('mobile')
                            ->label(__('common-fields.mobile'))
                            ->tel()
                            ->maxLength(20),
                        
                        TextInput::make('country_code')
                            ->label(__('app-contacts.fields.country_code'))
                            ->maxLength(10),
                        
                        TextInput::make('company')
                            ->label(__('common-fields.company'))
                            ->maxLength(255),
                    ])
                    ->columns(2),
                
                Section::make(__('app-contacts.sections.additional_information.title'))
                    ->schema([
                        Select::make('gender')
                            ->label(__('common-fields.gender'))
                            ->options(GenderEnum::class)
                            ->searchable(),
                        
                        Select::make('country_id')
                            ->label(__('common-fields.country'))
                            ->required()
                            ->options(fn () => Country::all()->pluck('name', 'id')->mapWithKeys(fn ($name, $id) => [$id => is_array($name) ? ($name['en'] ?? $name['fa'] ?? current($name)) : $name]))
                            ->searchable()
                            ->preload(),
                        
                        Toggle::make('is_customer')
                            ->label(__('app-contacts.fields.mark_as_customer'))
                            ->helperText(__('app-contacts.helpers.mark_as_customer'))
                            ->default(false)
                            ->inline(false)
                            ->columnSpanFull(),
                        
                        Textarea::make('postal_address')
                            ->label(__('app-contacts.fields.postal_address'))
                            ->columnSpanFull()
                            ->rows(3),
                    ])
                    ->columns(2)
                    ->collapsible()
            ]);
    }
}
