<?php

namespace App\Filament\App\Resources\TenantContacts\Tables;

use App\Enums\ContactTypeEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;

class TenantContactsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->whereIn('type', [
                ContactTypeEnum::CUSTOMER,
                ContactTypeEnum::LEAD,
            ]))
            ->columns([
                TextColumn::make('type')
                    ->label(__('app-contacts.columns.type'))
                    ->badge()
                    ->sortable()
                    ->color(fn($state) => match($state) {
                        ContactTypeEnum::CUSTOMER => 'success',
                        ContactTypeEnum::LEAD => 'warning',
                        ContactTypeEnum::USER => 'info',
                        default => 'gray',
                    }),
                
                TextColumn::make('first_name')
                    ->label(__('app-contacts.fields.first_name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                TextColumn::make('last_name')
                    ->label(__('app-contacts.fields.last_name'))
                    ->searchable()
                    ->sortable()
                    ->placeholder(__('app-contacts.placeholders.not_provided')),
                
                TextColumn::make('email')
                    ->label(__('common-fields.email'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage(__('app-contacts.messages.email_copied'))
                    ->placeholder(__('app-contacts.placeholders.no_email'))
                    ->icon('heroicon-o-envelope'),
                
                TextColumn::make('phone')
                    ->label(__('common-fields.phone'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage(__('app-contacts.messages.phone_copied'))
                    ->placeholder(__('app-contacts.placeholders.no_phone'))
                    ->icon('heroicon-o-phone'),
                
                TextColumn::make('company')
                    ->label(__('common-fields.company'))
                    ->searchable()
                    ->sortable()
                    ->placeholder(__('app-contacts.placeholders.no_company'))
                    ->icon('heroicon-o-building-office'),
                
                TextColumn::make('country.name')
                    ->label(__('common-fields.country'))
                    ->searchable()
                    ->sortable()
                    ->placeholder(__('app-contacts.placeholders.not_specified'))
                    ->badge()
                    ->color('primary')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('created_at')
                    ->label(__('common-fields.created_at'))
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter(),
                
                TextColumn::make('updated_at')
                    ->label(__('common-fields.updated_at'))
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter(),
            ])
            ->filters([
                TernaryFilter::make('is_customer')
                    ->label(__('app-contacts.filters.customer_status'))
                    ->placeholder(__('app-contacts.filters.all_contacts'))
                    ->trueLabel(__('app-contacts.filters.customers_only'))
                    ->falseLabel(__('app-contacts.filters.leads_only'))
                    ->queries(
                        true: fn($query) => $query->where('type', ContactTypeEnum::CUSTOMER->value),
                        false: fn($query) => $query->where('type', ContactTypeEnum::LEAD->value),
                        blank: fn($query) => $query,
                    ),
            ])
            ->recordActions([
                ViewAction::make()
                    ->icon('heroicon-o-eye'),
                EditAction::make()
                    ->icon('heroicon-o-pencil'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(),
                    
                    BulkAction::make('export')
                        ->label(__('app-contacts.bulk_actions.export'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            // Export logic can be implemented here
                            Notification::make()
                                ->title(__('app-contacts.notifications.export_started_title'))
                                ->body(__('app-contacts.notifications.export_started_body'))
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('first_name')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('30s');
    }
}
