<?php

namespace App\Filament\Tenant\Resources\TenantUsers\Tables;

use App\Models\Base\Country;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TenantUsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Full Name')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user')
                    ->weight('medium'),
                
                TextColumn::make('email')
                    ->label('Email Address')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-envelope')
                    ->copyable()
                    ->copyMessage('Email copied!')
                    ->copyMessageDuration(1500),
                
                IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable()
                    ->alignCenter(),
                
                TextColumn::make('contact.phone')
                    ->label('Phone')
                    ->icon('heroicon-o-phone')
                    ->placeholder('—')
                    ->toggleable(),
                
                TextColumn::make('contact.company')
                    ->label('Company')
                    ->icon('heroicon-o-building-office')
                    ->placeholder('—')
                    ->toggleable()
                    ->searchable(),
                
                TextColumn::make('contact.country.name')
                    ->label('Country')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn ($state) => is_array($state) ? ($state['en'] ?? $state['fa'] ?? current($state)) : $state),
                
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('email_verified_at')
                    ->label('Email Verified')
                    ->placeholder('All Users')
                    ->trueLabel('Verified')
                    ->falseLabel('Not Verified')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('email_verified_at'),
                        false: fn ($query) => $query->whereNull('email_verified_at'),
                    ),
                
                SelectFilter::make('country')
                    ->label('Country')
                    ->options(fn () => Country::all()->pluck('name', 'id')->mapWithKeys(fn ($name, $id) => [$id => is_array($name) ? ($name['en'] ?? $name['fa'] ?? current($name)) : $name]))
                    ->searchable()
                    ->preload()
                    ->query(function ($query, $data) {
                        if (isset($data['value'])) {
                            return $query->whereHas('contact', function ($q) use ($data) {
                                $q->where('country_id', $data['value']);
                            });
                        }
                        return $query;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]), // Temporarily disabled
            ])
            ->defaultSort('created_at', 'desc');
    }
}
