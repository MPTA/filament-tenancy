<?php

namespace App\Filament\Tenant\Pages;

use App\Models\TenantSetting;
use App\Models\Base\Currency;
use App\Models\Base\Language;
use App\Models\Base\Country;
use App\Models\Base\City;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
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
    protected static ?string $title = 'Tenant Settings';
    protected static ?string $navigationLabel = 'Settings';
    protected static ?int $navigationSort = 999;

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
                    Section::make('Company Information')
                        ->description('Basic company details and information')
                        ->icon('heroicon-o-building-office')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('company_name')
                                        ->label('Company Name')
                                        ->maxLength(255)
                                        ->required(),
                                        
                                    TextInput::make('company_local_name')
                                        ->label('Company Local Name')
                                        ->maxLength(255),
                                ]),
                        ])
                        ->collapsible(),

                    Section::make('Contact Information')
                        ->description('Contact details and communication information')
                        ->icon('heroicon-o-phone')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('contact_name')
                                        ->label('Contact Name')
                                        ->maxLength(255),
                                        
                                    TextInput::make('phone_number')
                                        ->label('Phone Number')
                                        ->tel()
                                        ->maxLength(255),
                                ]),
                            
                            TextInput::make('mobile_number')
                                ->label('Mobile Number')
                                ->tel()
                                ->maxLength(255)
                                ->columnSpanFull(),
                                
                            Textarea::make('address')
                                ->label('Address')
                                ->rows(3)
                                ->columnSpanFull(),
                        ])
                        ->collapsible(),

                    Section::make('Location & Preferences')
                        ->description('Geographic location and system preferences')
                        ->icon('heroicon-o-globe-alt')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    Select::make('country_id')
                                        ->label('Country')
                                        ->options(Country::all()->pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->afterStateUpdated(fn () => $this->form->getComponent('city_id')->state(null)),
                                        
                                    Select::make('city_id')
                                        ->label('City')
                                        ->options(City::all()->pluck('name', 'id'))
                                        ->searchable()
                                        ->preload(),
                                ]),
                            
                            Grid::make(2)
                                ->schema([
                                    Select::make('currency_id')
                                        ->label('Currency')
                                        ->options(Currency::all()->pluck('name', 'id'))
                                        ->searchable()
                                        ->disabled()
                                        ->preload(),
                                        
                                    Select::make('language_id')
                                        ->label('Language')
                                        ->options(Language::all()->pluck('name', 'id'))
                                        ->searchable()
                                        ->preload(),
                                ]),
                        ])
                        ->collapsible(),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
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
            $idFields = ['country_id', 'city_id', 'currency_id', 'language_id'];
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
                    ->title('Settings updated successfully!')
                    ->success()
                    ->send();
            } else {
                // Create new record - tenant_id خودکار توسط BelongsToTenant trait تنظیم می‌شود
                TenantSetting::create($data);
                Notification::make()
                    ->title('Settings created successfully!')
                    ->success()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error saving settings!')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function getRecord()
    {
        return TenantSetting::first();
    }

    public static function canAccess(): bool
    {
        return true; // هر کاربری که لاگین کرده باشد می‌تواند دسترسی داشته باشد
    }
}
