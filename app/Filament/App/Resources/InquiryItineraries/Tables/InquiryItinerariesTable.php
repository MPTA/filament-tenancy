<?php

namespace App\Filament\App\Resources\InquiryItineraries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InquiryItinerariesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('inquiry.number')
                    ->label(__('app-inquiry-itineraries.columns.number'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                
                TextColumn::make('inquiry.title')
                    ->label(__('app-inquiry-itineraries.columns.title'))
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    }),
                
                TextColumn::make('inquiry.reference')
                    ->label(__('app-inquiry-itineraries.columns.reference'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),
                
                TextColumn::make('inquiry.contact.full_name')
                    ->label(__('app-inquiry-itineraries.columns.contact'))
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('quotations_count')
                    ->label(__('app-inquiry-itineraries.columns.quotations'))
                    ->state(function ($record) {
                        return $record->inquiry?->quotations()->count() ?? 0;
                    })
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray')
                    ->tooltip(fn ($state) => $state > 0 ? __('app-inquiry-itineraries.tooltips.quotations_count', ['count' => $state]) : __('app-inquiry-itineraries.tooltips.no_quotations')),
                
                TextColumn::make('quotation_numbers')
                    ->label(__('app-inquiry-itineraries.columns.quotation_numbers'))
                    ->state(function ($record) {
                        $quotations = $record->inquiry?->quotations ?? collect();
                        if ($quotations->isEmpty()) {
                            return '—';
                        }
                        
                        // ساخت لینک‌های HTML برای هر quotation
                        $links = $quotations->map(function ($quotation) {
                            // پیدا کردن QuotationItinerary مربوط به این quotation
                            $quotationItinerary = $quotation->quotationItinerary;
                            if ($quotationItinerary) {
                                $url = route('filament.app.resources.quotation-itineraries.view', ['record' => $quotationItinerary->id]);
                                return '<a href="' . $url . '" target="_blank" class="text-primary-600 hover:underline font-medium">' . $quotation->number . '</a>';
                            }
                            return $quotation->number;
                        })->join(', ');
                        
                        return new \Illuminate\Support\HtmlString($links);
                    })
                    ->html()
                    ->wrap(),
                
                TextColumn::make('created_at')
                    ->label(__('app-inquiry-itineraries.columns.created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label(__('app-inquiry-itineraries.columns.updated'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading(fn ($record) => __('app-inquiry-itineraries.modals.delete_heading', ['number' => $record->inquiry?->number ?? 'N/A']))
                    ->modalDescription(function ($record) {
                        $quotationsCount = $record->inquiry?->quotations()->count() ?? 0;
                        
                        if ($quotationsCount > 0) {
                            return __('app-inquiry-itineraries.modals.delete_description_with_quotations', ['count' => $quotationsCount]);
                        }
                        
                        return __('app-inquiry-itineraries.modals.delete_description');
                    })
                    ->disabled(fn ($record) => ($record->inquiry?->quotations()->count() ?? 0) > 0)
                    ->tooltip(function ($record) {
                        $quotationsCount = $record->inquiry?->quotations()->count() ?? 0;
                        
                        if ($quotationsCount > 0) {
                            return __('app-inquiry-itineraries.tooltips.cannot_delete_has_quotations', ['count' => $quotationsCount]);
                        }
                        
                        return null;
                    })
                    ->before(function ($record, DeleteAction $action) {
                        $quotationsCount = $record->inquiry?->quotations()->count() ?? 0;
                        
                        if ($quotationsCount > 0) {
                            Notification::make()
                                ->warning()
                                ->title(__('app-inquiry-itineraries.notifications.cannot_delete_title'))
                                ->body(__('app-inquiry-itineraries.notifications.cannot_delete_body', ['count' => $quotationsCount]))
                                ->persistent()
                                ->send();
                            
                            $action->cancel();
                        }
                    })
                    ->modalSubmitActionLabel(__('app-inquiry-itineraries.modals.delete_submit')),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
