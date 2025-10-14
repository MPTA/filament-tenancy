<?php

namespace App\Filament\App\Resources\QuotationItineraries\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Checkbox;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
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
                    ->label('Quotation Number')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                
                TextColumn::make('quotation.inquiry.title')
                    ->label('Inquiry Title')
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
                    ->label('Contact')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('quotation.currency.name')
                    ->label('Currency')
                    ->badge()
                    ->color('info'),
                
                TextColumn::make('quotation.exchange_rate')
                    ->label('Exchange Rate')
                    ->numeric(decimalPlaces: 4)
                    ->sortable(),
                
                TextColumn::make('quotation.expire_date')
                    ->label('Expires')
                    ->date()
                    ->sortable()
                    ->color(fn ($state) => $state < now() ? 'danger' : ($state < now()->addDays(7) ? 'warning' : 'success')),
                
                BadgeColumn::make('quotation.status')
                    ->label('Status')
                    ->colors([
                        'success' => 'Active',
                        'danger' => 'Expired',
                    ]),
                
                TextColumn::make('quotation.inquiry.inquiryItinerary.from_date')
                    ->label('Travel From')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('quotation.inquiry.inquiryItinerary.to_date')
                    ->label('Travel To')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('quotation_currency')
                    ->label('Currency')
                    ->relationship('quotation.currency', 'name'),
                
                SelectFilter::make('quotation_status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'expired' => 'Expired',
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
                    ->label('Customer View')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url(fn ($record) => route('filament.app.resources.quotation-itineraries.customer-view', ['record' => $record->id]))
                    ->openUrlInNewTab(),
                ActionGroup::make([
                    ViewAction::make(),
                    DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading(fn ($record) => 'Delete Quotation ' . ($record->quotation?->number ?? 'N/A'))
                        ->modalDescription(function ($record) {
                            $inquiry = $record->quotation?->inquiry;
                            $quotationsCount = $inquiry?->quotations()->count() ?? 0;
                            
                            $description = 'Are you sure you want to delete quotation itinerary "' . ($record->quotation?->number ?? 'N/A') . '"?';
                            
                            if ($quotationsCount === 1) {
                                $description .= "\n\nNote: This is the only quotation for inquiry #" . ($inquiry?->number ?? 'N/A') . '. You can choose to delete the inquiry as well.';
                            }
                            
                            return $description;
                        })
                        ->form(function ($record) {
                            $inquiry = $record->quotation?->inquiry;
                            $quotationsCount = $inquiry?->quotations()->count() ?? 0;
                            
                            // فقط اگر این تنها quotation برای inquiry است، checkbox نمایش بده
                            if ($quotationsCount === 1) {
                                return [
                                    Checkbox::make('delete_inquiry')
                                        ->label('Also delete the related inquiry (#' . ($inquiry?->number ?? 'N/A') . ') and all its data')
                                        ->helperText('Warning: This will permanently delete the inquiry, inquiry itinerary, and all related data.')
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
                                ->title('Deleted successfully')
                                ->body($deleteInquiry 
                                    ? 'Quotation and inquiry have been deleted.' 
                                    : 'Quotation has been deleted.')
                                ->send();
                        })
                        ->modalSubmitActionLabel('Delete'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
