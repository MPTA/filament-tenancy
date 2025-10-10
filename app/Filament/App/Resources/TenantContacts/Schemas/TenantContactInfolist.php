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
                Section::make('Contact Details')
                    ->schema([
                        TextEntry::make('first_name')
                            ->label('First Name')
                            ->weight('bold')
                            ->size('lg'),
                        
                        TextEntry::make('last_name')
                            ->label('Last Name')
                            ->placeholder('Not provided'),
                        
                        TextEntry::make('email')
                            ->label('Email Address')
                            ->placeholder('Not provided')
                            ->copyable()
                            ->copyMessage('Email copied')
                            ->icon('heroicon-o-envelope'),
                        
                        TextEntry::make('phone')
                            ->label('Phone')
                            ->placeholder('Not provided')
                            ->copyable()
                            ->copyMessage('Phone copied')
                            ->icon('heroicon-o-phone'),
                        
                        TextEntry::make('mobile')
                            ->label('Mobile')
                            ->placeholder('Not provided')
                            ->copyable()
                            ->copyMessage('Mobile copied')
                            ->icon('heroicon-o-device-phone-mobile'),
                        
                        TextEntry::make('company')
                            ->label('Company')
                            ->placeholder('Not provided')
                            ->icon('heroicon-o-building-office'),
                    ])
                    ->columns(2),
                
                Section::make('Additional Information')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('User')
                            ->placeholder('Not assigned')
                            ->icon('heroicon-o-user'),
                        
                        
                        TextEntry::make('gender')
                            ->label('Gender')
                            ->placeholder('Not specified')
                            ->badge()
                            ->color(fn($state) => match($state) {
                                GenderEnum::MALE => 'info',
                                GenderEnum::FEMALE => 'danger',
                                default => 'gray',
                            }),
                        
                        IconEntry::make('type')
                            ->label('Is Customer')
                            ->icon(fn($state) => $state === ContactTypeEnum::CUSTOMER ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                            ->color(fn($state) => $state === ContactTypeEnum::CUSTOMER ? 'success' : 'gray'),
                        
                        TextEntry::make('country.name')
                            ->label('Country')
                            ->placeholder('Not specified')
                            ->badge()
                            ->color('primary'),
                        
                        TextEntry::make('country_code')
                            ->label('Country Code')
                            ->placeholder('Not specified')
                            ->badge()
                            ->color('warning'),
                    ])
                    ->columns(2)
                    ->collapsible(),
                
                Section::make('Address')
                    ->schema([
                        TextEntry::make('postal_address')
                            ->label('Postal Address')
                            ->placeholder('No address provided')
                            ->columnSpanFull()
                            ->icon('heroicon-o-map-pin'),
                    ])
                    ->collapsible(),
                
                Section::make('System Information')
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID')
                            ->badge()
                            ->color('gray'),
                        
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('M j, Y g:i A')
                            ->placeholder('Not available')
                            ->icon('heroicon-o-calendar'),
                        
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime('M j, Y g:i A')
                            ->placeholder('Not available')
                            ->icon('heroicon-o-pencil'),
                    ])
                    ->columns(3)
                    ->collapsible()
            ]);
    }
}
