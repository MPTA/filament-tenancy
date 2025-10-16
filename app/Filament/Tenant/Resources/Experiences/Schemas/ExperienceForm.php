<?php

namespace App\Filament\Tenant\Resources\Experiences\Schemas;

use App\Enums\ChargeModeEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ExperienceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('tenant-experiences.sections.basic_information.title'))
                    ->description(__('tenant-experiences.sections.basic_information.description'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('tenant-experiences.fields.experience_name'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('tenant-experiences.placeholders.name'))
                            ->helperText(__('tenant-experiences.helpers.name'))
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $set('slug', Str::slug($state));
                                }
                            }),
                        
                        TextInput::make('slug')
                            ->label(__('common-fields.slug'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder(__('tenant-experiences.placeholders.slug'))
                            ->helperText(__('tenant-experiences.helpers.slug'))
                            ->rules(['regex:/^[a-z0-9-]+$/']),
                        
                        Textarea::make('description')
                            ->label(__('common-fields.description'))
                            ->maxLength(1000)
                            ->placeholder(__('tenant-experiences.placeholders.description'))
                            ->helperText(__('tenant-experiences.helpers.description'))
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        RichEditor::make('content')
                            ->label(__('tenant-experiences.fields.content'))
                            ->placeholder(__('tenant-experiences.placeholders.content'))
                            ->helperText(__('tenant-experiences.helpers.content'))
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                
                Section::make(__('tenant-experiences.sections.pricing_location.title'))
                    ->description(__('tenant-experiences.sections.pricing_location.description'))
                    ->schema([
                        TextInput::make('price')
                            ->label(__('common-fields.price'))
                            ->prefix(fn() => tenant()->settings?->country?->currency?->symbol ?? '$')
                            ->placeholder(__('tenant-experiences.placeholders.price'))
                            ->helperText(__('tenant-experiences.helpers.price'))
                            ->rules(['nullable', 'numeric', 'min:0'])
                            ->inputMode('decimal'),
                        
                        Select::make('charge_mode')
                            ->label(__('common-fields.charge_mode'))
                            ->options(ChargeModeEnum::getOptions())
                            ->default('per_person')
                            ->required()
                            ->searchable()
                            ->placeholder(__('tenant-experiences.placeholders.charge_mode'))
                            ->helperText(__('tenant-experiences.helpers.charge_mode')),
                        
                        Toggle::make('is_free_for_guide')
                            ->label(__('common-fields.is_free_for_guide'))
                            ->default(false)
                            ->helperText(__('tenant-experiences.helpers.is_free_for_guide')),
                        
                        Toggle::make('is_free_for_other_companions')
                            ->label(__('common-fields.is_free_for_other_companions'))
                            ->default(false)
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $set('is_free_for_guide', true);
                                }
                            })
                            ->helperText(__('tenant-experiences.helpers.is_free_for_other_companions')),
                    ])
                    ->columns(2),
                
                Section::make(__('tenant-experiences.sections.location_details.title'))
                    ->description(__('tenant-experiences.sections.location_details.description'))
                    ->schema([
                        Textarea::make('address')
                            ->label(__('common-fields.address'))
                            ->maxLength(500)
                            ->placeholder(__('tenant-experiences.placeholders.address'))
                            ->helperText(__('tenant-experiences.helpers.address'))
                            ->rows(3),
                        
                        Select::make('city_id')
                            ->label(__('common-fields.city'))
                            ->relationship('city', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder(__('tenant-experiences.placeholders.city'))
                            ->helperText(__('tenant-experiences.helpers.city'))
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('province_id')
                                    ->relationship('province', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                            ]),
                        
                        Select::make('district_id')
                            ->label(__('common-fields.district'))
                            ->relationship('district', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder(__('tenant-experiences.placeholders.district'))
                            ->helperText(__('tenant-experiences.helpers.district'))
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('city_id')
                                    ->relationship('city', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ])
                    ->columns(2)
            ]);
    }
}
