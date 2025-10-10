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
            ->columns([
                TextColumn::make('type')
                ->label('Type')
                ->badge()
                ->sortable()
                ->color(fn($state) => match($state) {
                    ContactTypeEnum::CUSTOMER => 'success',
                    ContactTypeEnum::LEAD => 'warning',
                    ContactTypeEnum::USER => 'info',
                    default => 'gray',
                }),
                TextColumn::make('first_name')
                    ->label('First Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                TextColumn::make('last_name')
                    ->label('Last Name')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Not provided'),
                
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Email copied')
                    ->placeholder('No email')
                    ->icon('heroicon-o-envelope'),
                
                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Phone copied')
                    ->placeholder('No phone')
                    ->icon('heroicon-o-phone'),
                
                TextColumn::make('company')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->placeholder('No company')
                    ->icon('heroicon-o-building-office'),
                

                
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Not assigned')
                    ->icon('heroicon-o-user'),
                
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter(),
                
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter(),
            ])
            ->filters([
                TernaryFilter::make('is_customer')
                    ->label('Customer Status')
                    ->placeholder('All contacts')
                    ->trueLabel('Customers only')
                    ->falseLabel('Leads only')
                    ->queries(
                        true: fn($query) => $query->where('type', ContactTypeEnum::CUSTOMER->value),
                        false: fn($query) => $query->where('type', ContactTypeEnum::LEAD->value),
                        blank: fn($query) => $query,
                    ),
                
                SelectFilter::make('user_id')
                    ->label('Assigned User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
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
                        ->label('Export Selected')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            // Export logic can be implemented here
                            Notification::make()
                                ->title('Export Started')
                                ->body('Selected contacts will be exported.')
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
