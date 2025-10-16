<?php

namespace App\Filament\Tenant\Resources\Experiences\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExperienceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('tenant-experiences.sections.experience_details.title'))
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('tenant-experiences.fields.experience_name'))
                            ->weight('bold')
                            ->size('lg'),
                        
                        TextEntry::make('slug')
                            ->label(__('common-fields.slug'))
                            ->badge()
                            ->color('primary')
                            ->copyable()
                            ->copyMessage(__('tenant-experiences.messages.slug_copied'))
                            ->copyMessageDuration(1500),
                        
                        TextEntry::make('charge_mode')
                            ->label(__('common-fields.charge_mode'))
                            ->badge()
                            ->color('success'),
                        
                        IconEntry::make('is_active')
                            ->label(__('common-fields.status'))
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                        
                        IconEntry::make('is_free_for_guide')
                            ->label(__('common-fields.is_free_for_guide'))
                            ->boolean()
                            ->trueIcon('heroicon-o-gift')
                            ->falseIcon('heroicon-o-currency-dollar')
                            ->trueColor('success')
                            ->falseColor('gray'),
                        
                        IconEntry::make('is_free_for_other_companions')
                            ->label(__('tenant-experiences.fields.free_for_companions'))
                            ->boolean()
                            ->trueIcon('heroicon-o-gift')
                            ->falseIcon('heroicon-o-currency-dollar')
                            ->trueColor('success')
                            ->falseColor('gray'),
                    ])
                    ->columns(2),
                
                Section::make(__('tenant-experiences.sections.description_content.title'))
                    ->schema([
                        TextEntry::make('description')
                            ->label(__('common-fields.description'))
                            ->placeholder(__('tenant-experiences.placeholders.no_description'))
                            ->markdown()
                            ->columnSpanFull(),
                        
                        TextEntry::make('content')
                            ->label(__('tenant-experiences.fields.content'))
                            ->placeholder(__('tenant-experiences.placeholders.no_content'))
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                
                Section::make(__('tenant-experiences.sections.pricing_information.title'))
                    ->schema([
                        TextEntry::make('price')
                            ->label(__('tenant-experiences.fields.price_default_currency'))
                            ->money('USD')
                            ->placeholder(__('tenant-experiences.placeholders.free'))
                            ->icon('heroicon-o-currency-dollar'),
                    ])
                    ->columns(1),
                
                Section::make(__('tenant-experiences.sections.location_information.title'))
                    ->schema([
                        TextEntry::make('address')
                            ->label(__('common-fields.address'))
                            ->placeholder(__('tenant-experiences.placeholders.no_address'))
                            ->icon('heroicon-o-map-pin')
                            ->columnSpanFull(),
                        
                        TextEntry::make('city.name')
                            ->label(__('common-fields.city'))
                            ->badge()
                            ->color('info')
                            ->placeholder(__('tenant-experiences.placeholders.not_specified')),
                        
                        TextEntry::make('district.name')
                            ->label(__('common-fields.district'))
                            ->badge()
                            ->color('info')
                            ->placeholder(__('tenant-experiences.placeholders.not_specified')),
                    ])
                    ->columns(2),
                
                Section::make(__('tenant-experiences.sections.system_information.title'))
                    ->schema([
                        TextEntry::make('id')
                            ->label(__('common-fields.id'))
                            ->badge()
                            ->color('gray'),
                        
                        TextEntry::make('creator.name')
                            ->label(__('common-fields.created_by'))
                            ->placeholder(__('tenant-experiences.placeholders.unknown'))
                            ->icon('heroicon-o-user'),
                        
                        TextEntry::make('created_at')
                            ->label(__('common-fields.created_at_full'))
                            ->dateTime('M j, Y g:i A')
                            ->placeholder(__('tenant-experiences.placeholders.not_available'))
                            ->icon('heroicon-o-calendar'),
                        
                        TextEntry::make('updated_at')
                            ->label(__('common-fields.updated_at_full'))
                            ->dateTime('M j, Y g:i A')
                            ->placeholder(__('tenant-experiences.placeholders.not_available'))
                            ->icon('heroicon-o-pencil'),
                    ])
                    ->columns(2)
                    ->collapsible()
            ]);
    }
}
