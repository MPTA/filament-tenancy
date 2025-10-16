<?php

namespace App\Filament\Tenant\Pages;

use App\Models\TenantSetting;
use App\Models\Base\Currency;
use App\Models\Base\Language;
use App\Models\Base\Country;
use App\Models\Base\City;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use BackedEnum;

class TenantSettings extends Page
{
    protected string $view = 'filament.tenant.pages.tenant-settings';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?int $navigationSort = 999;
    
    public static function getNavigationLabel(): string
    {
        return __('tenant-settings.navigation_label');
    }
    
    public function getTitle(): string
    {
        return __('tenant-settings.title');
    }

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $record = $this->getRecord();
        if ($record) {
            $this->data = $record->toArray();
        } else {
            $this->data = [
                'tenant_id' => tenant('id'),
            ];
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Section::make(__('tenant-settings.sections.company_information.title'))
                        ->description(__('tenant-settings.sections.company_information.description'))
                        ->icon('heroicon-o-building-office')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('company_name')
                                        ->label(__('common-fields.company_name'))
                                        ->maxLength(255)
                                        ->required(),
                                        
                                    TextInput::make('company_local_name')
                                        ->label(__('common-fields.company_local_name'))
                                        ->maxLength(255),
                                ]),
                            
                            FileUpload::make('logo')
                                ->label(__('common-fields.company_logo'))
                                ->image()
                                ->disk('public')
                                ->directory(fn () => TenantSetting::getTenantDirectory('logos'))
                                ->visibility('public')
                                ->maxSize(2048)
                                ->fetchFileInformation(false)
                                ->imageEditor()
                                ->imageEditorAspectRatios([
                                    null,
                                    '16:9',
                                    '4:3',
                                    '1:1',
                                ])
                                ->helperText(__('tenant-settings.helpers.logo_upload'))
                                ->columnSpanFull(),

                            FileUpload::make('signature')
                                ->label(__('common-fields.company_signature'))
                                ->image()
                                ->disk('public')
                                ->directory(fn () => TenantSetting::getTenantDirectory('signatures'))
                                ->visibility('public')
                                ->maxSize(2048)
                                ->fetchFileInformation(false)
                                ->imageEditor()
                                ->imageEditorAspectRatios([
                                    null,
                                    '16:9',
                                    '4:3',
                                    '1:1',
                                ])
                                ->helperText(__('tenant-settings.helpers.signature_upload'))
                                ->columnSpanFull(),
                        ])
                        ->collapsible(),

                    Section::make(__('tenant-settings.sections.contact_information.title'))
                        ->description(__('tenant-settings.sections.contact_information.description'))
                        ->icon('heroicon-o-phone')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('contact_name')
                                        ->label(__('common-fields.contact_name'))
                                        ->maxLength(255),
                                        
                                    TextInput::make('phone_number')
                                        ->label(__('common-fields.phone_number'))
                                        ->tel()
                                        ->maxLength(255),
                                ]),
                            
                            TextInput::make('mobile_number')
                                ->label(__('common-fields.mobile_number'))
                                ->tel()
                                ->maxLength(255)
                                ->columnSpanFull(),
                                
                            Textarea::make('address')
                                ->label(__('common-fields.address'))
                                ->rows(3)
                                ->columnSpanFull(),
                        ])
                        ->collapsible(),

                    Section::make(__('tenant-settings.sections.location_preferences.title'))
                        ->description(__('tenant-settings.sections.location_preferences.description'))
                        ->icon('heroicon-o-globe-alt')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    Select::make('country_id')
                                        ->label(__('common-fields.country'))
                                        ->options(Country::all()->pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->afterStateUpdated(fn () => $this->form->getComponent('city_id')->state(null)),
                                        
                                    Select::make('city_id')
                                        ->label(__('common-fields.city'))
                                        ->options(City::all()->pluck('name', 'id'))
                                        ->searchable()
                                        ->preload(),
                                ]),
                            
                            Select::make('language_id')
                                ->label(__('common-fields.language'))
                                ->options(Language::all()->pluck('name', 'id'))
                                ->searchable()
                                ->preload(),
                            
                            Placeholder::make('currency_display')
                                ->label(__('common-fields.default_currency'))
                                ->content(function () {
                                    $record = $this->getRecord();
                                    $currency = $record?->country?->currency;
                                    if ($currency) {
                                        return $currency->code . ' (' . $currency->symbol . ')';
                                    }
                                    return __('tenant-settings.placeholders.no_currency');
                                })
                                ->helperText(__('tenant-settings.helpers.currency_auto')),
                        ])
                        ->collapsible(),

                    Section::make(__('tenant-settings.sections.base_budget_configuration.title'))
                        ->description(__('tenant-settings.sections.base_budget_configuration.description'))
                        ->icon('heroicon-o-banknotes')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('driver_meal_base_budget')
                                        ->label(__('common-fields.driver_meal_base_budget'))
                                        ->numeric()
                                        ->prefix('$')
                                        ->placeholder(__('tenant-settings.placeholders.driver_meal_budget'))
                                        ->default(50.00)
                                        ->helperText(__('tenant-settings.helpers.driver_meal_per_day')),
                                        
                                    TextInput::make('driver_accommodation_base_budget')
                                        ->label(__('common-fields.driver_accommodation_base_budget'))
                                        ->numeric()
                                        ->prefix('$')
                                        ->placeholder(__('tenant-settings.placeholders.driver_accommodation_budget'))
                                        ->default(100.00)
                                        ->helperText(__('tenant-settings.helpers.driver_accommodation_per_night')),
                                ]),
                            
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('companion_meal_base_budget')
                                        ->label(__('common-fields.companion_meal_base_budget'))
                                        ->numeric()
                                        ->prefix('$')
                                        ->placeholder(__('tenant-settings.placeholders.companion_meal_budget'))
                                        ->default(50.00)
                                        ->helperText(__('tenant-settings.helpers.companion_meal_per_day')),
                                        
                                    TextInput::make('companion_accommodation_base_budget')
                                        ->label(__('common-fields.companion_accommodation_base_budget'))
                                        ->numeric()
                                        ->prefix('$')
                                        ->placeholder(__('tenant-settings.placeholders.companion_accommodation_budget'))
                                        ->default(100.00)
                                        ->helperText(__('tenant-settings.helpers.companion_accommodation_per_night')),
                                ]),
                        ])
                        ->collapsible(),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label(__('tenant-settings.actions.save'))
                                ->submit('save')
                                ->keyBindings(['mod+s'])
                                ->color('success')
                                ->icon('heroicon-o-check'),
                        ]),
                    ]),
            ])
            ->record($this->getRecord())
            ->statePath('data');
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();
            
            // بررسی اینکه آیا فیلدهای ID در داده‌ها وجود دارند
            $idFields = ['country_id', 'city_id', 'language_id'];
            foreach ($idFields as $field) {
                if (!isset($data[$field]) || $data[$field] === '') {
                    $data[$field] = null;
                }
            }

            $record = $this->getRecord();
            
            if ($record) {
                // Update existing record
                $record->update($data);
                Notification::make()
                    ->title(__('tenant-settings.notifications.updated_success'))
                    ->success()
                    ->send();
            } else {
                // Create new record - tenant_id خودکار توسط BelongsToTenant trait تنظیم می‌شود
                TenantSetting::create($data);
                Notification::make()
                    ->title(__('tenant-settings.notifications.created_success'))
                    ->success()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title(__('tenant-settings.notifications.error_title'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function getRecord()
    {
        return tenant()->settings;
    }

    public static function canAccess(): bool
    {
        return true; // هر کاربری که لاگین کرده باشد می‌تواند دسترسی داشته باشد
    }
}
