<?php

namespace App\Filament\App\Resources\TenantContacts\Schemas;

use App\Enums\ContactTypeEnum;
use App\Enums\GenderEnum;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TenantContactInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app-contacts.sections.contact_details.title'))
                    ->schema([
                        TextEntry::make('first_name')
                            ->label(__('app-contacts.fields.first_name'))
                            ->weight('bold')
                            ->size('lg'),
                        
                        TextEntry::make('last_name')
                            ->label(__('app-contacts.fields.last_name'))
                            ->placeholder(__('app-contacts.placeholders.not_provided')),
                        
                        TextEntry::make('email')
                            ->label(__('app-contacts.fields.email_address'))
                            ->placeholder(__('app-contacts.placeholders.not_provided'))
                            ->copyable()
                            ->copyMessage(__('app-contacts.messages.email_copied'))
                            ->icon('heroicon-o-envelope'),
                        
                        TextEntry::make('phone')
                            ->label(__('common-fields.phone'))
                            ->placeholder(__('app-contacts.placeholders.not_provided'))
                            ->copyable()
                            ->copyMessage(__('app-contacts.messages.phone_copied'))
                            ->icon('heroicon-o-phone'),
                        
                        TextEntry::make('mobile')
                            ->label(__('common-fields.mobile'))
                            ->placeholder(__('app-contacts.placeholders.not_provided'))
                            ->copyable()
                            ->copyMessage(__('app-contacts.messages.mobile_copied'))
                            ->icon('heroicon-o-device-phone-mobile'),
                        
                        TextEntry::make('company')
                            ->label(__('common-fields.company'))
                            ->placeholder(__('app-contacts.placeholders.not_provided'))
                            ->icon('heroicon-o-building-office'),
                    ])
                    ->columns(2),
                
                Section::make(__('app-contacts.sections.additional_information.title'))
                    ->schema([
                        TextEntry::make('gender')
                            ->label(__('common-fields.gender'))
                            ->placeholder(__('app-contacts.placeholders.not_specified'))
                            ->badge()
                            ->color(fn($state) => match($state) {
                                GenderEnum::MALE => 'info',
                                GenderEnum::FEMALE => 'danger',
                                default => 'gray',
                            }),
                        
                        IconEntry::make('type')
                            ->label(__('app-contacts.fields.is_customer'))
                            ->icon(fn($state) => $state === ContactTypeEnum::CUSTOMER ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                            ->color(fn($state) => $state === ContactTypeEnum::CUSTOMER ? 'success' : 'gray'),
                        
                        TextEntry::make('country.name')
                            ->label(__('common-fields.country'))
                            ->placeholder(__('app-contacts.placeholders.not_specified'))
                            ->badge()
                            ->color('primary'),
                        
                        TextEntry::make('country_code')
                            ->label(__('app-contacts.fields.country_code'))
                            ->placeholder(__('app-contacts.placeholders.not_specified'))
                            ->badge()
                            ->color('warning'),
                    ])
                    ->columns(2)
                    ->collapsible(),
                
                Section::make(__('app-contacts.sections.address.title'))
                    ->schema([
                        TextEntry::make('postal_address')
                            ->label(__('app-contacts.fields.postal_address'))
                            ->placeholder(__('app-contacts.placeholders.no_address_provided'))
                            ->columnSpanFull()
                            ->icon('heroicon-o-map-pin'),
                    ])
                    ->collapsible(),
                
                Section::make(__('app-contacts.sections.system_information.title'))
                    ->schema([
                        TextEntry::make('id')
                            ->label(__('common-fields.id'))
                            ->badge()
                            ->color('gray'),
                        
                        TextEntry::make('created_at')
                            ->label(__('common-fields.created_at_full'))
                            ->dateTime('M j, Y g:i A')
                            ->placeholder(__('app-contacts.placeholders.not_available'))
                            ->icon('heroicon-o-calendar'),
                        
                        TextEntry::make('updated_at')
                            ->label(__('common-fields.updated_at_full'))
                            ->dateTime('M j, Y g:i A')
                            ->placeholder(__('app-contacts.placeholders.not_available'))
                            ->icon('heroicon-o-pencil'),
                    ])
                    ->columns(3)
                    ->collapsible()
            ]);
    }
}
