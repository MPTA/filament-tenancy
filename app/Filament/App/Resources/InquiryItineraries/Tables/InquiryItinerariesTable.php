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
                    ->label('Number')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                
                TextColumn::make('inquiry.title')
                    ->label('Title')
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
                    ->label('Reference')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),
                
                TextColumn::make('inquiry.contact.full_name')
                    ->label('Contact')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('quotations_count')
                    ->label('Quotations')
                    ->state(function ($record) {
                        return $record->inquiry?->quotations()->count() ?? 0;
                    })
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray')
                    ->tooltip(fn ($state) => $state > 0 ? $state . ' quotation(s)' : 'No quotations'),
                
                TextColumn::make('quotation_numbers')
                    ->label('Quotation Numbers')
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
                //
            ])
            ->recordActions([
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading(fn ($record) => 'Delete Inquiry #' . ($record->inquiry?->number ?? 'N/A'))
                    ->modalDescription(function ($record) {
                        $quotationsCount = $record->inquiry?->quotations()->count() ?? 0;
                        
                        if ($quotationsCount > 0) {
                            return 'Cannot delete this inquiry because it has ' . $quotationsCount . ' quotation(s). Please delete all quotations first.';
                        }
                        
                        return 'Are you sure you want to delete this inquiry itinerary? This action cannot be undone.';
                    })
                    ->disabled(fn ($record) => ($record->inquiry?->quotations()->count() ?? 0) > 0)
                    ->tooltip(function ($record) {
                        $quotationsCount = $record->inquiry?->quotations()->count() ?? 0;
                        
                        if ($quotationsCount > 0) {
                            return 'This inquiry has ' . $quotationsCount . ' quotation(s). Delete all quotations first.';
                        }
                        
                        return null;
                    })
                    ->before(function ($record, DeleteAction $action) {
                        $quotationsCount = $record->inquiry?->quotations()->count() ?? 0;
                        
                        if ($quotationsCount > 0) {
                            Notification::make()
                                ->warning()
                                ->title('Cannot delete inquiry')
                                ->body('This inquiry has ' . $quotationsCount . ' quotation(s). Please delete all quotations first.')
                                ->persistent()
                                ->send();
                            
                            $action->cancel();
                        }
                    })
                    ->modalSubmitActionLabel('Delete'),
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
