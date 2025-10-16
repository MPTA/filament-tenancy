<?php

namespace App\Filament\Tenant\Resources\MealTypes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MealTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('tenant-meal-types.sections.meal_type_details.title'))
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('tenant-meal-types.fields.meal_type_name'))
                            ->weight('bold')
                            ->size('lg'),
                        
                        TextEntry::make('slug')
                            ->label(__('common-fields.slug'))
                            ->badge()
                            ->color('primary')
                            ->copyable()
                            ->copyMessage(__('tenant-meal-types.messages.slug_copied'))
                            ->copyMessageDuration(1500),
                        
                        TextEntry::make('mealCategory.name')
                            ->label(__('tenant-meal-types.fields.meal_category'))
                            ->badge()
                            ->color('success'),
                    ])
                    ->columns(3),
                
                Section::make(__('tenant-meal-types.sections.description_pricing.title'))
                    ->schema([
                        TextEntry::make('description')
                            ->label(__('common-fields.description'))
                            ->placeholder(__('tenant-meal-types.placeholders.no_description_provided'))
                            ->markdown()
                            ->columnSpanFull(),
                        
                        TextEntry::make('price')
                            ->label(__('common-fields.price'))
                            ->money('USD')
                            ->placeholder(__('tenant-meal-types.placeholders.not_specified'))
                            ->icon('heroicon-o-currency-dollar'),
                    ])
                    ->columns(1),
                
                Section::make(__('tenant-meal-types.sections.system_information.title'))
                    ->schema([
                        TextEntry::make('id')
                            ->label(__('common-fields.id'))
                            ->badge()
                            ->color('gray'),
                        
                        TextEntry::make('created_at')
                            ->label(__('common-fields.created_at_full'))
                            ->dateTime('M j, Y g:i A')
                            ->placeholder(__('tenant-meal-types.placeholders.not_available'))
                            ->icon('heroicon-o-calendar'),
                        
                        TextEntry::make('updated_at')
                            ->label(__('common-fields.updated_at_full'))
                            ->dateTime('M j, Y g:i A')
                            ->placeholder(__('tenant-meal-types.placeholders.not_available'))
                            ->icon('heroicon-o-pencil'),
                    ])
                    ->columns(3)
                    ->collapsible()
            ]);
    }
}
