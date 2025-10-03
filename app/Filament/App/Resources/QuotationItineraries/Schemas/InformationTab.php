<?php

namespace App\Filament\App\Resources\QuotationItineraries\Schemas;

use App\Models\Tenants\QuotationItinerary;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
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
                            ->color('success'),

                        TextEntry::make('quotation.id')
                            ->label('Requested Currency')
                            ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->requestedCurrency?->name ?? 'Not specified')
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
                            ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->inquiryItinerary?->date_type?->value ?? 'Not specified')
                            ->icon('heroicon-o-calendar')
                            ->color('primary'),
                    ]),

                Grid::make(2)
                    ->schema([
                        TextEntry::make('quotation.id')
                            ->label('From Date')
                            ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->inquiryItinerary?->from_date?->format('Y-m-d') ?? 'Not specified')
                            ->icon('heroicon-o-calendar-days')
                            ->color('success'),

                        TextEntry::make('quotation.id')
                            ->label('To Date')
                            ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->inquiryItinerary?->to_date?->format('Y-m-d') ?? 'Not specified')
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
                Grid::make(2)
                    ->schema([
                        TextEntry::make('quotation.number')
                            ->label('Quotation Number')
                            ->icon('heroicon-o-hashtag')
                            ->color('primary'),

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
                    $inquiry->save();
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

                TextInput::make('quotation.exchange_rate')
                    ->label('Exchange Rate')
                    ->numeric()
                    ->step(0.0001),

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
}
