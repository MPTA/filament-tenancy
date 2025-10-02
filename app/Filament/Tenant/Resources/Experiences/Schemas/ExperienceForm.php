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
                Section::make('Basic Information')
                    ->description('Enter the basic experience details')
                    ->schema([
                        TextInput::make('name')
                            ->label('Experience Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., City Walking Tour, Cultural Experience')
                            ->helperText('Full name of the experience')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $set('slug', Str::slug($state));
                                }
                            }),
                        
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('e.g., city-walking-tour, cultural-experience')
                            ->helperText('URL-friendly identifier (auto-generated from name)')
                            ->rules(['regex:/^[a-z0-9-]+$/']),
                        
                        Textarea::make('description')
                            ->label('Description')
                            ->maxLength(1000)
                            ->placeholder('Brief description of the experience')
                            ->helperText('Short description for listings and previews')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        RichEditor::make('content')
                            ->label('Content')
                            ->placeholder('Detailed content about the experience')
                            ->helperText('Full content with all details about the experience')
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
                
                Section::make('Pricing & Location')
                    ->description('Set pricing and location information')
                    ->schema([
                        TextInput::make('price')
                            ->label('Price')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('0.00')
                            ->helperText('Price for the experience'),
                        
                        Select::make('currency_id')
                            ->label('Currency')
                            ->relationship('currency', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Select currency')
                            ->helperText('Currency for pricing'),
                        
                        Select::make('charge_mode')
                            ->label('Charge Mode')
                            ->options(ChargeModeEnum::class)
                            ->default('per_person')
                            ->required()
                            ->searchable()
                            ->placeholder('Select charge mode')
                            ->helperText('How the experience is charged'),
                        
                        Toggle::make('is_active')
                            ->label('Active')
                            ->required()
                            ->default(true)
                            ->helperText('Whether this experience is available for booking'),
                        
                        Toggle::make('is_free_for_guide')
                            ->label('Free for Guide')
                            ->default(false)
                            ->helperText('Whether this experience is free for tour guides'),
                        
                        Toggle::make('is_free_for_other_companions')
                            ->label('Free for Other Companions')
                            ->default(false)
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $set('is_free_for_guide', true);
                                }
                            })
                            ->helperText('Whether this experience is free for other companions (automatically enables free for guide)'),
                    ])
                    ->columns(2),
                
                Section::make('Location Details')
                    ->description('Specify the location of the experience')
                    ->schema([
                        Textarea::make('address')
                            ->label('Address')
                            ->maxLength(500)
                            ->placeholder('Full address of the experience location')
                            ->helperText('Complete address including street, building, etc.')
                            ->rows(3),
                        
                        Select::make('city_id')
                            ->label('City')
                            ->relationship('city', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder('Select a city')
                            ->helperText('City where the experience takes place')
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
                            ->label('District')
                            ->relationship('district', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Select a district')
                            ->helperText('District within the city')
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
