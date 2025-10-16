<?php

namespace App\Filament\Tenant\Resources\VehicleTypes\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VehicleTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('tenant-vehicle-types.sections.vehicle_type_details.title'))
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('tenant-vehicle-types.fields.vehicle_type_name'))
                            ->weight('bold')
                            ->size('lg'),
                        
                        TextEntry::make('slug')
                            ->label(__('common-fields.slug'))
                            ->badge()
                            ->color('primary')
                            ->copyable()
                            ->copyMessage(__('tenant-vehicle-types.messages.slug_copied'))
                            ->copyMessageDuration(1500),
                        
                        TextEntry::make('vehicleCategory.name')
                            ->label(__('tenant-vehicle-types.fields.vehicle_category'))
                            ->badge()
                            ->color('success'),
                        
                        IconEntry::make('is_vip')
                            ->label(__('tenant-vehicle-types.fields.vip_service'))
                            ->boolean()
                            ->trueIcon('heroicon-o-star')
                            ->falseIcon('heroicon-o-star')
                            ->trueColor('warning')
                            ->falseColor('gray'),
                    ])
                    ->columns(2),
                
                Section::make(__('tenant-vehicle-types.sections.capacity_specifications.title'))
                    ->schema([
                        TextEntry::make('capacity_from')
                            ->label(__('tenant-vehicle-types.fields.min_capacity'))
                            ->suffix(' ' . __('tenant-vehicle-types.suffixes.passengers'))
                            ->placeholder(__('tenant-vehicle-types.placeholders.not_specified'))
                            ->icon('heroicon-o-users'),
                        
                        TextEntry::make('capacity_to')
                            ->label(__('tenant-vehicle-types.fields.max_capacity'))
                            ->suffix(' ' . __('tenant-vehicle-types.suffixes.passengers'))
                            ->placeholder(__('tenant-vehicle-types.placeholders.not_specified'))
                            ->icon('heroicon-o-users'),
                        
                        TextEntry::make('max_hour_per_day')
                            ->label(__('tenant-vehicle-types.fields.max_hours_day'))
                            ->suffix(' ' . __('tenant-vehicle-types.suffixes.hours'))
                            ->placeholder(__('tenant-vehicle-types.placeholders.not_specified'))
                            ->icon('heroicon-o-clock'),
                        
                        TextEntry::make('max_hour_half_day')
                            ->label(__('tenant-vehicle-types.fields.max_hours_half_day_short'))
                            ->suffix(' ' . __('tenant-vehicle-types.suffixes.hours'))
                            ->placeholder(__('tenant-vehicle-types.placeholders.not_specified'))
                            ->icon('heroicon-o-clock'),
                    ])
                    ->columns(2),
                
                Section::make(__('tenant-vehicle-types.sections.pricing_information.title'))
                    ->schema([
                        TextEntry::make('per_day_price')
                            ->label(__('tenant-vehicle-types.fields.per_day_price'))
                            ->money('USD')
                            ->placeholder(__('tenant-vehicle-types.placeholders.not_specified'))
                            ->icon('heroicon-o-currency-dollar'),
                        
                        TextEntry::make('half_day_price')
                            ->label(__('tenant-vehicle-types.fields.half_day_price'))
                            ->money('USD')
                            ->placeholder(__('tenant-vehicle-types.placeholders.not_specified'))
                            ->icon('heroicon-o-currency-dollar'),
                        
                        TextEntry::make('extra_hour_price')
                            ->label(__('tenant-vehicle-types.fields.extra_hour_price'))
                            ->money('USD')
                            ->placeholder(__('tenant-vehicle-types.placeholders.not_specified'))
                            ->icon('heroicon-o-currency-dollar'),
                        
                        TextEntry::make('airport_transfer_price')
                            ->label(__('tenant-vehicle-types.fields.airport_transfer_price'))
                            ->money('USD')
                            ->placeholder(__('tenant-vehicle-types.placeholders.not_specified'))
                            ->icon('heroicon-o-currency-dollar'),
                    ])
                    ->columns(2),
                
                Section::make(__('tenant-vehicle-types.sections.media_description.title'))
                    ->schema([
                        TextEntry::make('cover')
                            ->label(__('tenant-vehicle-types.fields.cover_image'))
                            ->url(fn ($record) => $record->cover)
                            ->openUrlInNewTab()
                            ->placeholder(__('tenant-vehicle-types.placeholders.no_cover_image'))
                            ->icon('heroicon-o-photo'),
                        
                        TextEntry::make('description')
                            ->label(__('common-fields.description'))
                            ->placeholder(__('tenant-vehicle-types.placeholders.no_description_provided'))
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
                
                Section::make(__('tenant-vehicle-types.sections.system_information.title'))
                    ->schema([
                        TextEntry::make('id')
                            ->label(__('common-fields.id'))
                            ->badge()
                            ->color('gray'),
                        
                        TextEntry::make('created_at')
                            ->label(__('common-fields.created_at_full'))
                            ->dateTime('M j, Y g:i A')
                            ->placeholder(__('tenant-vehicle-types.placeholders.not_available'))
                            ->icon('heroicon-o-calendar'),
                        
                        TextEntry::make('updated_at')
                            ->label(__('common-fields.updated_at_full'))
                            ->dateTime('M j, Y g:i A')
                            ->placeholder(__('tenant-vehicle-types.placeholders.not_available'))
                            ->icon('heroicon-o-pencil'),
                    ])
                    ->columns(3)
                    ->collapsible()
            ]);
    }
}
