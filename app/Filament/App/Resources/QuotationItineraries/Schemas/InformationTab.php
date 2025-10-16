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
        return Tab::make(__('app-quotation-itineraries.tabs.information'))
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
        return Section::make(__('app-quotation-itineraries.sections.inquiry_information.title'))
            ->description(__('app-quotation-itineraries.sections.inquiry_information.description'))
            ->icon('heroicon-o-document-text')
            ->headerActions([
                self::editInquiryAction(),
            ])
            ->schema([
                Grid::make(2)
                    ->schema([
                        TextEntry::make('quotation.id')
                            ->label(__('app-quotation-itineraries.fields.title'))
                            ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->getTranslation('title', app()->getLocale()) ?? __('app-quotation-itineraries.placeholders.no_title'))
                            ->icon('heroicon-o-tag')
                            ->color('primary'),

                        TextEntry::make('quotation.id')
                            ->label(__('app-quotation-itineraries.fields.inquiry_number'))
                            ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->number ?? __('app-quotation-itineraries.placeholders.no_number'))
                            ->icon('heroicon-o-hashtag')
                            ->color('info'),
                    ]),

                Grid::make(2)
                    ->schema([
                        TextEntry::make('quotation.id')
                            ->label(__('app-quotation-itineraries.fields.contact'))
                            ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->contact?->full_name ?? __('app-quotation-itineraries.placeholders.no_contact'))
                            ->icon('heroicon-o-user')
                            ->color('success')
                            ->action(
                                Action::make('viewContact')
                                    ->label(__('app-quotation-itineraries.actions.view_contact_details'))
                                    ->icon('heroicon-o-user-circle')
                                    ->modalHeading(fn($record) => $record->quotation?->inquiry?->contact?->full_name ?? __('app-quotation-itineraries.actions.view_contact_details'))
                                    ->modalDescription(__('app-quotation-itineraries.sections.contact_information.title'))
                                    ->modalSubmitAction(false)
                                    ->modalCancelActionLabel(__('app-quotation-itineraries.actions.close'))
                                    ->infolist(fn($record) => self::getContactInfolist($record->quotation?->inquiry?->contact))
                                    ->disabled(fn($record) => !$record->quotation?->inquiry?->contact)
                            ),

                        TextEntry::make('quotation.id')
                            ->label(__('app-quotation-itineraries.fields.requested_currency'))
                            ->formatStateUsing(fn($state, $record) => 
                                $record->quotation?->inquiry?->requestedCurrency 
                                    ? "{$record->quotation->inquiry->requestedCurrency->code} ({$record->quotation->inquiry->requestedCurrency->symbol})"
                                    : __('app-quotation-itineraries.placeholders.not_specified')
                            )
                            ->icon('heroicon-o-banknotes')
                            ->color('info'),
                    ]),

                Grid::make(2)
                    ->schema([
                        TextEntry::make('quotation.id')
                            ->label(__('app-quotation-itineraries.fields.reference'))
                            ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->reference ?? __('app-quotation-itineraries.placeholders.no_reference'))
                            ->icon('heroicon-o-link')
                            ->color('warning'),

                        TextEntry::make('quotation.id')
                            ->label(__('app-quotation-itineraries.fields.date_type'))
                            ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->inquiryItinerary?->date_type?->label() ?? __('app-quotation-itineraries.placeholders.not_specified'))
                            ->icon('heroicon-o-calendar')
                            ->color('primary'),
                    ]),

                Grid::make(2)
                    ->schema([
                        TextEntry::make('quotation.id')
                            ->label(__('app-quotation-itineraries.fields.from_date'))
                            ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->inquiryItinerary?->from_date?->format('M d, Y') ?? __('app-quotation-itineraries.placeholders.not_specified'))
                            ->icon('heroicon-o-calendar-days')
                            ->color('success'),

                        TextEntry::make('quotation.id')
                            ->label(__('app-quotation-itineraries.fields.to_date'))
                            ->formatStateUsing(fn($state, $record) => $record->quotation?->inquiry?->inquiryItinerary?->to_date?->format('M d, Y') ?? __('app-quotation-itineraries.placeholders.not_specified'))
                            ->icon('heroicon-o-calendar-days')
                            ->color('warning'),
                    ]),

                TextEntry::make('attachments_list')
                    ->label(__('app-quotation-itineraries.fields.attachments'))
                    ->state(function ($record) {
                        $attachments = $record->quotation?->inquiry?->attachments;
                        if (!$attachments || !is_array($attachments) || empty($attachments)) {
                            return __('app-quotation-itineraries.placeholders.no_attachments');
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
        return Section::make(__('app-quotation-itineraries.sections.quotation_information.title'))
            ->description(__('app-quotation-itineraries.sections.quotation_information.description'))
            ->icon('heroicon-o-currency-dollar')
            ->headerActions([
                self::editQuotationAction(),
            ])
            ->schema([
                Grid::make(3)
                    ->schema([
                        TextEntry::make('quotation.number')
                            ->label(__('app-quotation-itineraries.fields.quotation_number'))
                            ->icon('heroicon-o-hashtag')
                            ->color('primary'),

                        TextEntry::make('quotation.currency.code')
                            ->label(__('app-quotation-itineraries.fields.currency'))
                            ->formatStateUsing(fn($state, $record) => 
                                $record->quotation?->currency 
                                    ? "{$record->quotation->currency->code} ({$record->quotation->currency->symbol})"
                                    : __('app-quotation-itineraries.placeholders.not_specified')
                            )
                            ->icon('heroicon-o-currency-dollar')
                            ->color('success'),

                        TextEntry::make('quotation.exchange_rate')
                            ->label(__('app-quotation-itineraries.fields.exchange_rate'))
                            ->formatStateUsing(fn($state) => $state ? number_format($state, 4) : __('app-quotation-itineraries.placeholders.not_specified'))
                            ->icon('heroicon-o-arrow-path')
                            ->color('info'),
                    ]),

                Grid::make(3)
                    ->schema([
                        TextEntry::make('quotation.expire_date')
                            ->label(__('app-quotation-itineraries.fields.expiry_date'))
                            ->date()
                            ->icon('heroicon-o-calendar-days')
                            ->color('danger'),

                        TextEntry::make('entry_date')
                            ->label(__('app-quotation-itineraries.fields.entry_date_arrival'))
                            ->formatStateUsing(fn($state) => $state ? $state->format('M d, Y') : __('app-quotation-itineraries.placeholders.not_specified'))
                            ->icon('heroicon-o-calendar-days')
                            ->color('success'),

                        TextEntry::make('roomCategoriesDisplay')
                            ->label(__('app-quotation-itineraries.fields.room_categories'))
                            ->state(function ($record) {
                                if (empty($record->room_category_ids)) {
                                    return __('app-quotation-itineraries.placeholders.default_room_categories');
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
                    ->label(__('app-quotation-itineraries.fields.description'))
                    ->formatStateUsing(fn($state) => $state ?? __('app-quotation-itineraries.placeholders.no_description'))
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull(),

                TextEntry::make('quotation.internal_note')
                    ->label(__('app-quotation-itineraries.fields.internal_note'))
                    ->formatStateUsing(fn($state) => $state ?? __('app-quotation-itineraries.placeholders.no_internal_note'))
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('warning')
                    ->columnSpanFull(),

                IconEntry::make('is_foreigner_passengers')
                    ->label(__('app-quotation-itineraries.fields.foreigner_passengers'))
                    ->boolean()
                    ->icon(fn($state) => $state ? 'heroicon-o-globe-alt' : 'heroicon-o-home')
                    ->color(fn($state) => $state ? 'info' : 'gray')
                    ->columnSpanFull(),
            ]);
    }

    private static function editInquiryAction(): Action
    {
        return Action::make('edit_inquiry')
            ->label(__('app-quotation-itineraries.actions.edit_inquiry'))
            ->icon('heroicon-m-pencil-square')
            ->color('primary')
            ->schema([
                TextInput::make('inquiry.title')
                    ->label(__('app-quotation-itineraries.fields.title'))
                    ->required(),

                TextInput::make('inquiry.number')
                    ->label(__('app-quotation-itineraries.fields.inquiry_number'))
                    ->disabled()
                    ->dehydrated(),

                Select::make('inquiry.requested_currency_id')
                    ->label(__('app-quotation-itineraries.fields.currency'))
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
                    ->helperText(__('app-quotation-itineraries.helpers.currency_sync')),

                TextInput::make('inquiry.reference')
                    ->label(__('app-quotation-itineraries.fields.reference')),

                FileUpload::make('inquiry.attachments')
                    ->label(__('app-quotation-itineraries.fields.attachments'))
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
                    ->helperText(__('app-quotation-itineraries.helpers.attachments'))
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
                    ->title(__('app-quotation-itineraries.notifications.inquiry_updated_title'))
                    ->success()
                    ->send();
            })
            ->modalHeading(__('app-quotation-itineraries.actions.edit_inquiry'))
            ->modalSubmitActionLabel(__('app-quotation-itineraries.actions.save_changes'));
    }

    private static function editQuotationAction(): Action
    {
        return Action::make('edit_quotation')
            ->label(__('app-quotation-itineraries.actions.edit_quotation'))
            ->icon('heroicon-m-pencil-square')
            ->color('primary')
            ->schema([
                TextInput::make('quotation.number')
                    ->label(__('app-quotation-itineraries.fields.quotation_number'))
                    ->disabled()
                    ->dehydrated(),

                Select::make('quotation.currency_id')
                    ->label(__('app-quotation-itineraries.fields.currency'))
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
                    ->helperText(__('app-quotation-itineraries.helpers.currency_sync_quotation')),

                TextInput::make('quotation.exchange_rate')
                    ->label(__('app-quotation-itineraries.fields.exchange_rate'))
                    ->required()
                    ->rules(['required', 'regex:/^\d+(\.\d{1,4})?$/'])
                    ->helperText(__('app-quotation-itineraries.helpers.exchange_rate_format'))
                    ->placeholder(__('app-quotation-itineraries.placeholders.exchange_rate_modal'))
                    ->validationMessages([
                        'regex' => __('app-quotation-itineraries.validations.exchange_rate_regex'),
                    ]),

                DatePicker::make('quotation.expire_date')
                    ->label(__('app-quotation-itineraries.fields.expiry_date')),

                Textarea::make('quotation.description')
                    ->label(__('app-quotation-itineraries.fields.description'))
                    ->rows(3)
                    ->helperText(__('app-quotation-itineraries.helpers.description_visible')),

                Textarea::make('quotation.internal_note')
                    ->label(__('app-quotation-itineraries.fields.internal_note'))
                    ->rows(3)
                    ->helperText(__('app-quotation-itineraries.helpers.internal_note_private')),

                Select::make('room_category_ids')
                    ->label(__('app-quotation-itineraries.fields.room_categories'))
                    ->required()
                    ->multiple()
                    ->maxItems(3)
                    ->options(\App\Models\Base\RoomCategory::where('is_active', true)
                        ->get()
                        ->mapWithKeys(fn($cat) => [$cat->id => $cat->category->getDisplayName()])
                    )
                    ->searchable()
                    ->preload()
                    ->helperText(__('app-quotation-itineraries.helpers.room_categories_regenerate')),

                Toggle::make('is_foreigner_passengers')
                    ->label(__('app-quotation-itineraries.fields.foreigner_passengers'))
                    ->helperText(__('app-quotation-itineraries.helpers.foreigner_passengers_affects')),

                DatePicker::make('entry_date')
                    ->label(__('app-quotation-itineraries.fields.entry_date_arrival'))
                    ->disabled(fn(QuotationItinerary $record) => $record->transportations()->count() > 0)
                    ->helperText(fn(QuotationItinerary $record) => $record->transportations()->count() > 0 
                        ? __('app-quotation-itineraries.helpers.entry_date_controlled')
                        : __('app-quotation-itineraries.helpers.entry_date_arrival')),
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
                    ->title(__('app-quotation-itineraries.notifications.quotation_updated_title'))
                    ->body($needsBreakdownRegeneration && $record->breakdown
                        ? __('app-quotation-itineraries.messages.breakdown_regenerated', ['reason' => $regenerationReason])
                        : __('app-quotation-itineraries.messages.quotation_updated'))
                    ->success()
                    ->send();
            })
            ->modalHeading(__('app-quotation-itineraries.actions.edit_quotation'))
            ->modalSubmitActionLabel(__('app-quotation-itineraries.actions.save_changes'));
    }

    private static function getContactInfolist($contact): array
    {
        if (!$contact) {
            return [
                TextEntry::make('no_contact')
                    ->label('')
                    ->formatStateUsing(fn() => __('app-quotation-itineraries.placeholders.no_contact_info'))
                    ->color('gray'),
            ];
        }

        return [
            Section::make(__('app-quotation-itineraries.sections.personal_information.title'))
                ->icon('heroicon-o-user')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextEntry::make('first_name')
                                ->label(__('app-quotation-itineraries.fields.first_name'))
                                ->default($contact->first_name ?? 'N/A')
                                ->icon('heroicon-o-user'),

                            TextEntry::make('last_name')
                                ->label(__('app-quotation-itineraries.fields.last_name'))
                                ->default($contact->last_name ?? 'N/A')
                                ->icon('heroicon-o-user'),
                        ]),

                    Grid::make(2)
                        ->schema([
                            TextEntry::make('gender')
                                ->label(__('common-fields.gender'))
                                ->default($contact->gender ?? __('app-quotation-itineraries.placeholders.not_specified'))
                                ->icon('heroicon-o-identification')
                                ->badge()
                                ->color(fn() => match($contact->gender ?? null) {
                                    GenderEnum::MALE => 'info',
                                    GenderEnum::FEMALE => 'danger',
                                    default => 'gray',
                                }),

                            TextEntry::make('company')
                                ->label(__('common-fields.company'))
                                ->default($contact->company ?? 'N/A')
                                ->icon('heroicon-o-building-office'),
                        ]),
                ]),

            Section::make(__('app-quotation-itineraries.sections.contact_information.title'))
                ->icon('heroicon-o-phone')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextEntry::make('email')
                                ->label(__('common-fields.email'))
                                ->default($contact->email ?? 'N/A')
                                ->icon('heroicon-o-envelope')
                                ->copyable()
                                ->copyMessage(__('app-quotation-itineraries.messages.email_copied'))
                                ->color('primary'),

                            TextEntry::make('phone')
                                ->label(__('common-fields.phone'))
                                ->default($contact->phone ?? 'N/A')
                                ->icon('heroicon-o-phone')
                                ->copyable()
                                ->copyMessage(__('app-quotation-itineraries.messages.phone_copied')),
                        ]),

                    TextEntry::make('mobile')
                        ->label(__('app-quotation-itineraries.fields.mobile'))
                        ->default($contact->mobile ?? 'N/A')
                        ->icon('heroicon-o-device-phone-mobile')
                        ->copyable()
                        ->copyMessage(__('app-quotation-itineraries.messages.mobile_copied'))
                        ->columnSpanFull(),

                    TextEntry::make('postal_address')
                        ->label(__('app-quotation-itineraries.fields.postal_address'))
                        ->default($contact->postal_address ?? 'N/A')
                        ->icon('heroicon-o-map-pin')
                        ->columnSpanFull(),
                ]),

            Section::make(__('app-quotation-itineraries.sections.customer_status.title'))
                ->icon('heroicon-o-check-badge')
                ->schema([
                    IconEntry::make('type')
                        ->label(__('app-quotation-itineraries.fields.is_customer'))
                        ->icon(fn() => ($contact->type ?? ContactTypeEnum::LEAD) === ContactTypeEnum::CUSTOMER ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                        ->color(fn() => ($contact->type ?? ContactTypeEnum::LEAD) === ContactTypeEnum::CUSTOMER ? 'success' : 'gray')
                        ->label(fn() => ($contact->type ?? ContactTypeEnum::LEAD) === ContactTypeEnum::CUSTOMER ? __('app-quotation-itineraries.infolist.active_customer') : __('app-quotation-itineraries.infolist.lead_contact')),
                ]),
        ];
    }
}
