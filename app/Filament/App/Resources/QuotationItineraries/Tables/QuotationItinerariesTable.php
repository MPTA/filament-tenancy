<?php

namespace App\Filament\App\Resources\QuotationItineraries\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Checkbox;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class QuotationItinerariesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('quotation.number')
                    ->label(__('app-quotation-itineraries.columns.number'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                
                TextColumn::make('quotation.inquiry.title')
                    ->label(__('app-quotation-itineraries.columns.inquiry_title'))
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    }),
                
                TextColumn::make('quotation.inquiry.contact.full_name')
                    ->label(__('app-quotation-itineraries.columns.contact'))
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('offers_count')
                    ->label(__('app-quotation-itineraries.columns.offers'))
                    ->state(function ($record) {
                        return $record->quotationOfferGroups()->count();
                    })
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray')
                    ->tooltip(fn ($state) => $state > 0 ? __('app-quotation-itineraries.tooltips.offers_count', ['count' => $state]) : __('app-quotation-itineraries.tooltips.no_offers_yet')),
                
                TextColumn::make('quotation.expire_date')
                    ->label(__('app-quotation-itineraries.columns.expires'))
                    ->date()
                    ->sortable()
                    ->color(fn ($state) => $state < now() ? 'danger' : ($state < now()->addDays(7) ? 'warning' : 'success')),
                
                TextColumn::make('quotation.status')
                    ->label(__('app-quotation-itineraries.columns.status'))
                    ->badge()
                    ->color(fn ($state) => $state === 'Active' ? 'success' : 'danger'),
                
                TextColumn::make('created_at')
                    ->label(__('app-quotation-itineraries.columns.created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label(__('app-quotation-itineraries.columns.updated'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('quotation_status')
                    ->label(__('app-quotation-itineraries.filters.status'))
                    ->options([
                        'active' => __('app-quotation-itineraries.filters.active'),
                        'expired' => __('app-quotation-itineraries.filters.expired'),
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value'] === 'active') {
                            return $query->whereHas('quotation', function ($q) {
                                $q->where('expire_date', '>', now());
                            });
                        }
                        if ($data['value'] === 'expired') {
                            return $query->whereHas('quotation', function ($q) {
                                $q->where('expire_date', '<=', now());
                            });
                        }
                        return $query;
                    }),
            ])
            ->recordActions([
                Action::make('customerView')
                    ->label(__('app-quotation-itineraries.actions.customer_view'))
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url(fn ($record) => route('filament.app.resources.quotation-itineraries.customer-view', ['record' => $record->id]))
                    ->openUrlInNewTab(),
                ActionGroup::make([
                    ViewAction::make(),
                    DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading(fn ($record) => __('app-quotation-itineraries.modals.delete_heading', ['number' => $record->quotation?->number ?? 'N/A']))
                        ->modalDescription(function ($record) {
                            $inquiry = $record->quotation?->inquiry;
                            $quotationsCount = $inquiry?->quotations()->count() ?? 0;
                            
                            $description = __('app-quotation-itineraries.modals.delete_description', ['number' => $record->quotation?->number ?? 'N/A']);
                            
                            if ($quotationsCount === 1) {
                                $description = __('app-quotation-itineraries.modals.delete_description_only_quotation', [
                                    'number' => $record->quotation?->number ?? 'N/A',
                                    'inquiry_number' => $inquiry?->number ?? 'N/A'
                                ]);
                            }
                            
                            return $description;
                        })
                        ->schema(function ($record) {
                            $inquiry = $record->quotation?->inquiry;
                            $quotationsCount = $inquiry?->quotations()->count() ?? 0;
                            
                            // فقط اگر این تنها quotation برای inquiry است، checkbox نمایش بده
                            if ($quotationsCount === 1) {
                                return [
                                    Checkbox::make('delete_inquiry')
                                        ->label(__('app-quotation-itineraries.modals.delete_inquiry_checkbox', ['inquiry_number' => $inquiry?->number ?? 'N/A']))
                                        ->helperText(__('app-quotation-itineraries.modals.delete_inquiry_helper'))
                                        ->default(false),
                                ];
                            }
                            
                            return [];
                        })
                        ->action(function ($record, array $data) {
                            $quotationItinerary = $record;
                            $quotation = $quotationItinerary->quotation;
                            $inquiry = $quotation?->inquiry;
                            $deleteInquiry = $data['delete_inquiry'] ?? false;
                            
                            // شروع transaction
                            DB::transaction(function () use ($quotationItinerary, $quotation, $inquiry, $deleteInquiry) {
                                // حذف QuotationItinerary (این همه چیزهای مرتبط را cascade می‌کند)
                                $quotationItinerary->delete();
                                
                                // حذف Quotation
                                if ($quotation) {
                                    $quotation->delete();
                                }
                                
                                // اگر کاربر خواست inquiry هم حذف شود
                                if ($deleteInquiry && $inquiry) {
                                    // حذف InquiryItinerary (اگر وجود دارد)
                                    $inquiry->inquiryItinerary?->delete();
                                    
                                    // حذف Inquiry (باید تمام quotation های مربوطه حذف شده باشند)
                                    $inquiry->delete();
                                }
                            });
                            
                            // نمایش پیام موفقیت
                            Notification::make()
                                ->success()
                                ->title(__('app-quotation-itineraries.notifications.deleted_title'))
                                ->body($deleteInquiry 
                                    ? __('app-quotation-itineraries.notifications.deleted_quotation_and_inquiry')
                                    : __('app-quotation-itineraries.notifications.deleted_quotation_only'))
                                ->send();
                        })
                        ->modalSubmitActionLabel(__('app-quotation-itineraries.modals.delete_submit')),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
