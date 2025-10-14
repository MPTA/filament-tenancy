<?php

namespace App\Filament\App\Resources\QuotationItineraries\Schemas;

use App\Enums\ContactTypeEnum;
use App\Enums\GenderEnum;
use App\Models\Tenants\QuotationItinerary;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
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

                TextEntry::make('attachments_list')
                    ->label('Attachments')
                    ->state(function ($record) {
                        $attachments = $record->quotation?->inquiry?->attachments;
                        if (!$attachments || !is_array($attachments) || empty($attachments)) {
                            return 'No attachments';
                        }
                        return collect($attachments)
                            ->map(fn($file) => '📎 ' . basename($file))
                            ->join(' • ');
                    })
                    ->icon('heroicon-o-paper-clip')
                    ->color(fn($record) => !empty($record->quotation?->inquiry?->attachments) ? 'success' : 'gray')
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

                Grid::make(3)
                    ->schema([
                        TextEntry::make('quotation.expire_date')
                            ->label('Expiry Date')
                            ->date()
                            ->icon('heroicon-o-calendar-days')
                            ->color('danger'),

                        TextEntry::make('entry_date')
                            ->label('Entry Date (Arrival)')
                            ->formatStateUsing(fn($state) => $state ? $state->format('M d, Y') : 'Not specified')
                            ->icon('heroicon-o-calendar-days')
                            ->color('success'),

                        TextEntry::make('roomCategoriesDisplay')
                            ->label('Room Categories')
                            ->state(function ($record) {
                                if (empty($record->room_category_ids)) {
                                    return 'Default (Twin, Single)';
                                }
                                
                                $uniqueIds = array_values(array_unique($record->room_category_ids));
                                
                                $names = \App\Models\Base\RoomCategory::whereIn('id', $uniqueIds)
                                    ->pluck('category')
                                    ->map(fn($cat) => $cat->getDisplayName())
                                    ->toArray();
                                
                                return implode(', ', array_unique($names));
                            })
                            ->icon('heroicon-o-squares-2x2')
                            ->color('primary'),
                    ]),

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
                    ->options(function () {
                        // Load currencies once and format them
                        return \App\Models\Base\Currency::all()
                            ->mapWithKeys(fn($currency) => [
                                $currency->id => $currency->code . ' (' . $currency->symbol . ')'
                            ]);
                    })
                    ->searchable()
                    ->preload()
                    ->helperText('Changing currency will update both Inquiry and Quotation'),

                TextInput::make('inquiry.reference')
                    ->label('Reference'),

                FileUpload::make('inquiry.attachments')
                    ->label('Attachments')
                    ->multiple()
                    ->disk('local')
                    ->directory(fn () => \App\Models\TenantSetting::getTenantDirectory('inquiries'))
                    ->visibility('private')
                    ->downloadable()
                    ->openable()
                    ->deletable()
                    ->reorderable()
                    ->maxFiles(10)
                    ->maxSize(10240)
                    ->helperText('You can upload up to 10 files. Max size: 10MB per file.')
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                        'image/webp',
                    ]),
            ])
            ->fillForm(function (QuotationItinerary $record) {
                $inquiry = $record->quotation?->inquiry;

                return [
                    'inquiry' => $inquiry ? [
                        'title' => $inquiry->getTranslation('title', app()->getLocale()),
                        'number' => $inquiry->number,
                        'requested_currency_id' => $inquiry->requested_currency_id,
                        'reference' => $inquiry->reference,
                        'attachments' => $inquiry->attachments ?? [],
                    ] : [],
                ];
            })
            ->action(function (array $data, QuotationItinerary $record) {
                $inquiry = $record->quotation?->inquiry;

                // Update inquiry data
                if ($inquiry && isset($data['inquiry'])) {
                    $inquiry->setTranslation('title', app()->getLocale(), $data['inquiry']['title']);
                    $inquiry->reference = $data['inquiry']['reference'] ?? null;
                    
                    // Update requested_currency_id if provided
                    if (isset($data['inquiry']['requested_currency_id'])) {
                        $inquiry->requested_currency_id = $data['inquiry']['requested_currency_id'];
                    }
                    
                    // Update attachments if provided
                    if (isset($data['inquiry']['attachments'])) {
                        $inquiry->attachments = $data['inquiry']['attachments'];
                    }
                    
                    $inquiry->save();
                    
                    // Also update quotation currency_id to keep them in sync
                    if ($record->quotation && isset($data['inquiry']['requested_currency_id'])) {
                        $record->quotation->update([
                            'currency_id' => $data['inquiry']['requested_currency_id'],
                        ]);
                    }
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
                    ->options(function () {
                        // Load currencies once and format them
                        return \App\Models\Base\Currency::all()
                            ->mapWithKeys(fn($currency) => [
                                $currency->id => $currency->code . ' (' . $currency->symbol . ')'
                            ]);
                    })
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
                    ->rows(3)
                    ->helperText('This description will be visible to the customer in the quotation view.'),

                Textarea::make('quotation.internal_note')
                    ->label('Internal Note')
                    ->rows(3)
                    ->helperText('Internal note - NOT visible to the customer. Use this for team notes and reminders.'),

                Select::make('room_category_ids')
                    ->label('Room Categories')
                    ->required()
                    ->multiple()
                    ->maxItems(3)
                    ->options(\App\Models\Base\RoomCategory::where('is_active', true)
                        ->get()
                        ->mapWithKeys(fn($cat) => [$cat->id => $cat->category->getDisplayName()])
                    )
                    ->searchable()
                    ->preload()
                    ->helperText('Select up to 3 room types (required). Changing this will regenerate the breakdown.'),

                Toggle::make('is_foreigner_passengers')
                    ->label('Foreigner Passengers')
                    ->helperText('Enable if passengers are foreigners (affects attraction pricing)'),

                DatePicker::make('entry_date')
                    ->label('Entry Date (Arrival)')
                    ->disabled(fn(QuotationItinerary $record) => $record->transportations()->count() > 0)
                    ->helperText(fn(QuotationItinerary $record) => $record->transportations()->count() > 0 
                        ? 'Entry date is controlled by transportation. Remove transportation to edit manually.' 
                        : 'Entry date for the group arrival'),
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
                    'room_category_ids' => $record->room_category_ids ?? [],
                    'is_foreigner_passengers' => $record->is_foreigner_passengers,
                    'entry_date' => $record->entry_date,
                ];
            })
            ->action(function (array $data, QuotationItinerary $record) {
                $needsBreakdownRegeneration = false;
                $regenerationReason = '';

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

                // Check if room categories changed
                if (isset($data['room_category_ids'])) {
                    $oldRoomCategories = $record->room_category_ids ?? [];
                    $newRoomCategories = $data['room_category_ids'];
                    
                    if (json_encode($oldRoomCategories) !== json_encode($newRoomCategories)) {
                        $needsBreakdownRegeneration = true;
                        $regenerationReason = 'Room categories changed.';
                    }
                }

                // Check if passenger type changed
                if (isset($data['is_foreigner_passengers'])) {
                    $oldPassengerType = $record->getOriginal('is_foreigner_passengers');
                    if ($oldPassengerType !== $data['is_foreigner_passengers']) {
                        $needsBreakdownRegeneration = true;
                        $regenerationReason = $regenerationReason 
                            ? $regenerationReason . ' Passenger type changed.' 
                            : 'Passenger type changed.';
                    }
                }

                // Update quotation itinerary data
                $record->update([
                    'is_foreigner_passengers' => $data['is_foreigner_passengers'],
                    'room_category_ids' => $data['room_category_ids'] ?? [],
                    'entry_date' => $data['entry_date'] ?? null,
                ]);

                // Regenerate breakdown if needed
                if ($record->breakdown && $needsBreakdownRegeneration) {
                    $record->generateBreakdownFromItinerary();
                }

                // Refresh the record to update the UI
                $record->refresh();

                Notification::make()
                    ->title('Quotation updated successfully!')
                    ->body($needsBreakdownRegeneration && $record->breakdown
                        ? $regenerationReason . ' Breakdown has been regenerated.'
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
