<?php

namespace App\Filament\Resources\Tenants\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TenantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('ID copied!')
                    ->size('xs'),
                
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->description(function ($record) {
                        $domain = $record->domains()->first();
                        if ($domain) {
                            return request()->getScheme() . '://' . $domain->domain . '.' . config('filament-tenancy.central_domain');
                        }
                        return null;
                    })
                    ->weight('bold'),
                
                TextColumn::make('email')
                    ->label('Email')
                    ->icon('heroicon-o-envelope')
                    ->searchable()
                    ->copyable(),
                
                TextColumn::make('phone')
                    ->label('Phone')
                    ->icon('heroicon-o-phone')
                    ->copyable(),
                
                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('view')
                    ->label('View')
                    ->tooltip('Open tenant panel')
                    ->iconButton()
                    ->icon('heroicon-s-link')
                    ->color('primary')
                    ->url(fn ($record) => request()->getScheme() . '://' . ($record->domains()->first()?->domain ?? '') . '.' . config('filament-tenancy.central_domain') . '/app')
                    ->openUrlInNewTab(),
                
                EditAction::make()
                    ->iconButton(),
                
                DeleteAction::make()
                    ->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
