<?php

namespace App\Filament\App\Resources\QuotationItineraries\Schemas;

use App\Enums\ContactTypeEnum;
use App\Enums\GenderEnum;
use App\Models\Tenants\QuotationItinerary;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;

class InformationTab
{
    public static function getTab(): Tab
    {
        return Tab::make('Information')
            ->icon('heroicon-o-information-circle')
            ->badge('✓')
            ->badgeColor('success')
            ->schema([
                self::inquiryInformationSection(),
                self::quotationInformationSection(),
            ]);
    }

    private static function inquiryInformationSection(): Section
    {
        return Section::make('Inquiry Information')
            ->description('Basic inquiry details and information')
            ->icon('heroicon-o-document-text')
            ->headerActions([
                self::editInquiryAction(),
            ])
            ->schema([
                Grid::make(2)
                    ->schema([
                        TextEntry::make('quotation.id')
                            ->label('Title')
                            ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->getTranslation('title', app()->getLocale()) ?? 'No title')
                            ->icon('heroicon-o-tag')
                            ->color('primary'),

                        TextEntry::make('quotation.id')
                            ->label('Inquiry Number')
                            ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->number ?? 'No number')
                            ->icon('heroicon-o-hashtag')
                            ->color('info'),
                    ]),

                Grid::make(2)
                    ->schema([
                        TextEntry::make('quotation.id')
                            ->label('Contact')
                            ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->contact?->full_name ?? 'No contact')
                            ->icon('heroicon-o-user')
                            ->color('success')
                            ->action(
                                Action::make('viewContact')
                                    ->label('View Contact Details')
                                    ->icon('heroicon-o-user-circle')
                                    ->modalHeading(fn($record) => $record->quotation?->inquiry?->contact?->full_name ?? 'Contact Details')
                                    ->modalDescription('Complete information about this contact')
                                    ->modalSubmitAction(false)
                                    ->modalCancelActionLabel('Close')
                                    ->infolist(fn($record) => self::getContactInfolist($record->quotation?->inquiry?->contact))
                                    ->disabled(fn($record) => !$record->quotation?->inquiry?->contact)
                            ),

                        TextEntry::make('quotation.id')
                            ->label('Requested Currency')
                            ->formatStateUsing(fn($state, $record) => 
                                $record->quotation?->inquiry?->requestedCurrency 
                                    ? "{$record->quotation->inquiry->requestedCurrency->code} ({$record->quotation->inquiry->requestedCurrency->symbol})"
                                    : 'Not specified'
                            )
                            ->icon('heroicon-o-banknotes')
                            ->color('info'),
                    ]),

                Grid::make(2)
                    ->schema([
                        TextEntry::make('quotation.id')
                            ->label('Reference')
                            ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->reference ?? 'No reference')
                            ->icon('heroicon-o-link')
                            ->color('warning'),

                        TextEntry::make('quotation.id')
                            ->label('Date Type')
                            ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->inquiryItinerary?->date_type?->label() ?? 'Not specified')
                            ->icon('heroicon-o-calendar')
                            ->color('primary'),
                    ]),

                Grid::make(2)
                    ->schema([
                        TextEntry::make('quotation.id')
                            ->label('From Date')
                            ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->inquiryItinerary?->from_date?->format('M d, Y') ?? 'Not specified')
                            ->icon('heroicon-o-calendar-days')
                            ->color('success'),

                        TextEntry::make('quotation.id')
                            ->label('To Date')
                            ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->inquiryItinerary?->to_date?->format('M d, Y') ?? 'Not specified')
                            ->icon('heroicon-o-calendar-days')
                            ->color('warning'),
                    ]),

                TextEntry::make('quotation.id')
                    ->label('Description')
                    ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->getTranslation('description', app()->getLocale()) ?? 'No description')
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull(),
            ]);
    }

    private static function quotationInformationSection(): Section
    {
        return Section::make('Quotation Information')
            ->description('Quotation and pricing details')
            ->icon('heroicon-o-currency-dollar')
            ->headerActions([
                self::editQuotationAction(),
            ])
            ->schema([
                Grid::make(3)
                    ->schema([
                        TextEntry::make('quotation.number')
                            ->label('Quotation Number')
                            ->icon('heroicon-o-hashtag')
                            ->color('primary'),

                        TextEntry::make('quotation.currency.code')
                            ->label('Currency')
                            ->formatStateUsing(fn($state, $record) => 
                                $record->quotation?->currency 
                                    ? "{$record->quotation->currency->code} ({$record->quotation->currency->symbol})"
                                    : 'Not specified'
                            )
                            ->icon('heroicon-o-currency-dollar')
                            ->color('success'),

                        TextEntry::make('quotation.exchange_rate')
                            ->label('Exchange Rate')
                            ->formatStateUsing(fn($state) => $state ? number_format($state, 4) : 'Not specified')
                            ->icon('heroicon-o-arrow-path')
                            ->color('info'),
                    ]),

                TextEntry::make('quotation.expire_date')
                    ->label('Expiry Date')
                    ->date()
                    ->icon('heroicon-o-calendar-days')
                    ->color('danger')
                    ->columnSpanFull(),

                TextEntry::make('quotation.description')
                    ->label('Description')
                    ->formatStateUsing(fn($state) => $state ?? 'No description')
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull(),

                TextEntry::make('quotation.internal_note')
                    ->label('Internal Note')
                    ->formatStateUsing(fn($state) => $state ?? 'No internal note')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('warning')
                    ->columnSpanFull(),

                IconEntry::make('is_foreigner_passengers')
                    ->label('Foreigner Passengers')
                    ->boolean()
                    ->icon(fn($state) => $state ? 'heroicon-o-globe-alt' : 'heroicon-o-home')
                    ->color(fn($state) => $state ? 'info' : 'gray')
                    ->columnSpanFull(),
            ]);
    }

    private static function editInquiryAction(): Action
    {
        return Action::make('edit_inquiry')
            ->label('Edit Inquiry')
            ->icon('heroicon-m-pencil-square')
            ->color('primary')
            ->schema([
                TextInput::make('inquiry.title')
                    ->label('Title')
                    ->required(),

                TextInput::make('inquiry.number')
                    ->label('Inquiry Number')
                    ->disabled()
                    ->dehydrated(),

                Select::make('inquiry.requested_currency_id')
                    ->label('Currency')
                    ->required()
                    ->options(\App\Models\Base\Currency::all()->pluck('code', 'id')->mapWithKeys(fn($code, $id) => [
                        $id => \App\Models\Base\Currency::find($id)->code . ' (' . \App\Models\Base\Currency::find($id)->symbol . ')'
                    ]))
                    ->searchable()
                    ->preload()
                    ->helperText('Changing currency will update both Inquiry and Quotation'),

                Textarea::make('inquiry.description')
                    ->label('Description')
                    ->rows(3),

                TextInput::make('inquiry.reference')
                    ->label('Reference'),

                Select::make('inquiry_itinerary.date_type')
                    ->label('Date Type')
                    ->options(\App\Enums\InquiryDateTypeEnum::getOptions())
                    ->required(),

                DatePicker::make('inquiry_itinerary.from_date')
                    ->label('From Date')
                    ->required(),

                DatePicker::make('inquiry_itinerary.to_date')
                    ->label('To Date')
                    ->required(),
            ])
            ->fillForm(function (QuotationItinerary $record) {
                $inquiry = $record->quotation?->inquiry;
                $inquiryItinerary = $inquiry?->inquiryItinerary;

                return [
                    'inquiry' => $inquiry ? [
                        'title' => $inquiry->getTranslation('title', app()->getLocale()),
                        'number' => $inquiry->number,
                        'requested_currency_id' => $inquiry->requested_currency_id,
                        'description' => $inquiry->getTranslation('description', app()->getLocale()),
                        'reference' => $inquiry->reference,
                    ] : [],
                    'inquiry_itinerary' => $inquiryItinerary ? [
                        'date_type' => $inquiryItinerary->date_type?->value,
                        'from_date' => $inquiryItinerary->from_date,
                        'to_date' => $inquiryItinerary->to_date,
                    ] : [],
                ];
            })
            ->action(function (array $data, QuotationItinerary $record) {
                $inquiry = $record->quotation?->inquiry;
                $inquiryItinerary = $inquiry?->inquiryItinerary;

                // Update inquiry data
                if ($inquiry && isset($data['inquiry'])) {
                    $inquiry->setTranslation('title', app()->getLocale(), $data['inquiry']['title']);
                    $inquiry->setTranslation('description', app()->getLocale(), $data['inquiry']['description'] ?? '');
                    $inquiry->reference = $data['inquiry']['reference'] ?? null;
                    
                    // Update requested_currency_id if provided
                    if (isset($data['inquiry']['requested_currency_id'])) {
                        $inquiry->requested_currency_id = $data['inquiry']['requested_currency_id'];
                    }
                    
                    $inquiry->save();
                    
                    // Also update quotation currency_id to keep them in sync
                    if ($record->quotation && isset($data['inquiry']['requested_currency_id'])) {
                        $record->quotation->update([
                            'currency_id' => $data['inquiry']['requested_currency_id'],
                        ]);
                    }
                }

                // Update inquiry itinerary data
                if ($inquiryItinerary && isset($data['inquiry_itinerary'])) {
                    $inquiryItinerary->update([
                        'date_type' => $data['inquiry_itinerary']['date_type'],
                        'from_date' => $data['inquiry_itinerary']['from_date'],
                        'to_date' => $data['inquiry_itinerary']['to_date'],
                    ]);
                }

                // Refresh the record to update the UI
                $record->refresh();

                Notification::make()
                    ->title('Inquiry updated successfully!')
                    ->success()
                    ->send();
            })
            ->modalHeading('Edit Inquiry')
            ->modalSubmitActionLabel('Save Changes');
    }

    private static function editQuotationAction(): Action
    {
        return Action::make('edit_quotation')
            ->label('Edit Quotation')
            ->icon('heroicon-m-pencil-square')
            ->color('primary')
            ->schema([
                TextInput::make('quotation.number')
                    ->label('Quotation Number')
                    ->disabled()
                    ->dehydrated(),

                Select::make('quotation.currency_id')
                    ->label('Currency')
                    ->required()
                    ->options(\App\Models\Base\Currency::all()->pluck('code', 'id')->mapWithKeys(fn($code, $id) => [
                        $id => \App\Models\Base\Currency::find($id)->code . ' (' . \App\Models\Base\Currency::find($id)->symbol . ')'
                    ]))
                    ->searchable()
                    ->preload()
                    ->helperText('Changing currency will update both Quotation and Inquiry'),

                TextInput::make('quotation.exchange_rate')
                    ->label('Exchange Rate')
                    ->required()
                    ->rules(['required', 'regex:/^\d+(\.\d{1,4})?$/'])
                    ->helperText('Enter a valid number with up to 4 decimal places')
                    ->placeholder('e.g., 42500.5000')
                    ->validationMessages([
                        'regex' => 'Please enter a valid number with up to 4 decimal places.',
                    ]),

                DatePicker::make('quotation.expire_date')
                    ->label('Expiry Date'),

                Textarea::make('quotation.description')
                    ->label('Description')
                    ->rows(3),

                Textarea::make('quotation.internal_note')
                    ->label('Internal Note')
                    ->rows(3),

                Toggle::make('is_foreigner_passengers')
                    ->label('Foreigner Passengers')
                    ->helperText('Enable if passengers are foreigners (affects attraction pricing)'),
            ])
            ->fillForm(function (QuotationItinerary $record) {
                return [
                    'quotation' => $record->quotation ? [
                        'number' => $record->quotation->number,
                        'currency_id' => $record->quotation->currency_id,
                        'exchange_rate' => $record->quotation->exchange_rate,
                        'expire_date' => $record->quotation->expire_date,
                        'description' => $record->quotation->description,
                        'internal_note' => $record->quotation->internal_note,
                    ] : [],
                    'is_foreigner_passengers' => $record->is_foreigner_passengers,
                ];
            })
            ->action(function (array $data, QuotationItinerary $record) {
                // Update quotation data
                if ($record->quotation) {
                    $record->quotation->update($data['quotation']);
                    
                    // Also update inquiry requested_currency_id if currency changed
                    if ($record->quotation->inquiry && isset($data['quotation']['currency_id'])) {
                        $record->quotation->inquiry->update([
                            'requested_currency_id' => $data['quotation']['currency_id'],
                        ]);
                    }
                }

                // Update quotation itinerary data
                $record->update([
                    'is_foreigner_passengers' => $data['is_foreigner_passengers'],
                ]);

                // If breakdown exists and passenger type changed, regenerate it
                if ($record->breakdown && isset($data['is_foreigner_passengers'])) {
                    $oldPassengerType = $record->getOriginal('is_foreigner_passengers');
                    if ($oldPassengerType !== $data['is_foreigner_passengers']) {
                        // Passenger type changed, regenerate breakdown to update attraction prices
                        $record->generateBreakdownFromItinerary();
                    }
                }

                // Refresh the record to update the UI
                $record->refresh();

                Notification::make()
                    ->title('Quotation updated successfully!')
                    ->body($record->breakdown && isset($data['is_foreigner_passengers']) && $record->getOriginal('is_foreigner_passengers') !== $data['is_foreigner_passengers']
                        ? 'Passenger type changed. Breakdown has been regenerated with updated attraction prices.'
                        : 'Quotation information has been updated.')
                    ->success()
                    ->send();
            })
            ->modalHeading('Edit Quotation')
            ->modalSubmitActionLabel('Save Changes');
    }

    private static function getContactInfolist($contact): array
    {
        if (!$contact) {
            return [
                TextEntry::make('no_contact')
                    ->label('')
                    ->formatStateUsing(fn() => 'No contact information available')
                    ->color('gray'),
            ];
        }

        return [
            Section::make('Personal Information')
                ->icon('heroicon-o-user')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextEntry::make('first_name')
                                ->label('First Name')
                                ->default($contact->first_name ?? 'N/A')
                                ->icon('heroicon-o-user'),

                            TextEntry::make('last_name')
                                ->label('Last Name')
                                ->default($contact->last_name ?? 'N/A')
                                ->icon('heroicon-o-user'),
                        ]),

                    Grid::make(2)
                        ->schema([
                            TextEntry::make('gender')
                                ->label('Gender')
                                ->default($contact->gender ?? 'Not specified')
                                ->icon('heroicon-o-identification')
                                ->badge()
                                ->color(fn() => match($contact->gender ?? null) {
                                    GenderEnum::MALE => 'info',
                                    GenderEnum::FEMALE => 'danger',
                                    default => 'gray',
                                }),

                            TextEntry::make('company')
                                ->label('Company')
                                ->default($contact->company ?? 'N/A')
                                ->icon('heroicon-o-building-office'),
                        ]),
                ]),

            Section::make('Contact Information')
                ->icon('heroicon-o-phone')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextEntry::make('email')
                                ->label('Email')
                                ->default($contact->email ?? 'N/A')
                                ->icon('heroicon-o-envelope')
                                ->copyable()
                                ->copyMessage('Email copied!')
                                ->color('primary'),

                            TextEntry::make('phone')
                                ->label('Phone')
                                ->default($contact->phone ?? 'N/A')
                                ->icon('heroicon-o-phone')
                                ->copyable()
                                ->copyMessage('Phone copied!'),
                        ]),

                    TextEntry::make('mobile')
                        ->label('Mobile')
                        ->default($contact->mobile ?? 'N/A')
                        ->icon('heroicon-o-device-phone-mobile')
                        ->copyable()
                        ->copyMessage('Mobile copied!')
                        ->columnSpanFull(),

                    TextEntry::make('postal_address')
                        ->label('Postal Address')
                        ->default($contact->postal_address ?? 'N/A')
                        ->icon('heroicon-o-map-pin')
                        ->columnSpanFull(),
                ]),

            Section::make('Customer Status')
                ->icon('heroicon-o-check-badge')
                ->schema([
                    IconEntry::make('type')
                        ->label('Is Customer')
                        ->icon(fn() => ($contact->type ?? ContactTypeEnum::LEAD) === ContactTypeEnum::CUSTOMER ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                        ->color(fn() => ($contact->type ?? ContactTypeEnum::LEAD) === ContactTypeEnum::CUSTOMER ? 'success' : 'gray')
                        ->label(fn() => ($contact->type ?? ContactTypeEnum::LEAD) === ContactTypeEnum::CUSTOMER ? 'Active Customer' : 'Lead Contact'),
                ]),
        ];
    }
}
