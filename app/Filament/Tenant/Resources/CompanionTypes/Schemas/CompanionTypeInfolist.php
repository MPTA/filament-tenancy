<?php

namespace App\Filament\Tenant\Resources\CompanionTypes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompanionTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('tenant-companion-types.sections.basic_information.title'))
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('tenant-companion-types.fields.companion_type_name'))
                            ->weight('bold')
                            ->size('lg'),
                        
                        TextEntry::make('slug')
                            ->label(__('common-fields.slug'))
                            ->badge()
                            ->color('primary')
                            ->copyable()
                            ->copyMessage(__('tenant-companion-types.messages.slug_copied'))
                            ->copyMessageDuration(1500),
                        
                        TextEntry::make('companionCategory.name')
                            ->label(__('common-fields.companion_category'))
                            ->badge()
                            ->color('success'),
                    ])
                    ->columns(3),
                
                Section::make(__('tenant-companion-types.sections.language_requirements.title'))
                    ->schema([
                        TextEntry::make('nativeLanguage.name')
                            ->label(__('common-fields.native_language'))
                            ->badge()
                            ->color('info')
                            ->placeholder(__('tenant-companion-types.placeholders.not_specified')),
                        
                        TextEntry::make('speakingLanguage.name')
                            ->label(__('common-fields.speaking_language'))
                            ->badge()
                            ->color('info')
                            ->placeholder(__('tenant-companion-types.placeholders.not_specified')),
                    ])
                    ->columns(2),
                
                Section::make(__('tenant-companion-types.sections.pricing_information.title'))
                    ->schema([
                        TextEntry::make('per_day_price')
                            ->label(__('common-fields.per_day_price'))
                            ->money('USD')
                            ->placeholder(__('tenant-companion-types.placeholders.not_specified'))
                            ->icon('heroicon-o-currency-dollar'),
                        
                        TextEntry::make('half_day_price')
                            ->label(__('common-fields.half_day_price'))
                            ->money('USD')
                            ->placeholder(__('tenant-companion-types.placeholders.not_specified'))
                            ->icon('heroicon-o-currency-dollar'),
                        
                        TextEntry::make('per_hour_price')
                            ->label(__('common-fields.per_hour_price'))
                            ->money('USD')
                            ->placeholder(__('tenant-companion-types.placeholders.not_specified'))
                            ->icon('heroicon-o-clock'),
                        
                        TextEntry::make('extra_hour_price')
                            ->label(__('common-fields.extra_hour_price'))
                            ->money('USD')
                            ->placeholder(__('tenant-companion-types.placeholders.not_specified'))
                            ->icon('heroicon-o-plus-circle'),
                    ])
                    ->columns(2),
                
                Section::make(__('tenant-companion-types.sections.service_limits.title'))
                    ->schema([
                        TextEntry::make('max_hour_per_day')
                            ->label(__('common-fields.max_hours_per_day'))
                            ->suffix(__('tenant-companion-types.suffixes.hours'))
                            ->placeholder(__('tenant-companion-types.placeholders.not_specified'))
                            ->icon('heroicon-o-sun'),
                        
                        TextEntry::make('max_hour_half_day')
                            ->label(__('common-fields.max_hours_half_day'))
                            ->suffix(__('tenant-companion-types.suffixes.hours'))
                            ->placeholder(__('tenant-companion-types.placeholders.not_specified'))
                            ->icon('heroicon-o-moon'),
                    ])
                    ->columns(2),
                
                Section::make(__('tenant-companion-types.sections.system_information.title'))
                    ->schema([
                        TextEntry::make('id')
                            ->label(__('common-fields.id'))
                            ->badge()
                            ->color('gray'),
                        
                        TextEntry::make('created_at')
                            ->label(__('common-fields.created_at_full'))
                            ->dateTime('M j, Y g:i A')
                            ->placeholder(__('tenant-companion-types.placeholders.not_specified'))
                            ->icon('heroicon-o-calendar'),
                        
                        TextEntry::make('updated_at')
                            ->label(__('common-fields.updated_at_full'))
                            ->dateTime('M j, Y g:i A')
                            ->placeholder(__('tenant-companion-types.placeholders.not_specified'))
                            ->icon('heroicon-o-pencil'),
                    ])
                    ->columns(3)
                    ->collapsible()
            ]);
    }
}
