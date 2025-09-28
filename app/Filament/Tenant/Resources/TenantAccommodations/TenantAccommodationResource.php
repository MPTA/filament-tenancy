<?php

namespace App\Filament\Tenant\Resources\TenantAccommodations;

use App\Filament\Tenant\Resources\TenantAccommodations\Pages\ListTenantAccommodations;
use App\Filament\Tenant\Resources\TenantAccommodations\Pages\ViewTenantAccommodation;
use App\Filament\Tenant\Resources\TenantAccommodations\RelationManagers\TenantPricesRelationManager;
use App\Filament\Tenant\Resources\TenantAccommodations\Schemas\TenantAccommodationInfolist;
use App\Filament\Tenant\Resources\TenantAccommodations\Tables\TenantAccommodationsTable;
use App\Models\Base\Accommodation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TenantAccommodationResource extends Resource
{
    protected static ?string $model = Accommodation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;
    
    protected static ?string $navigationLabel = 'Accommodations';
    
    protected static ?string $modelLabel = 'Accommodation';
    
    protected static ?string $pluralModelLabel = 'Accommodations';
    
    protected static ?int $navigationSort = 4;

    public static function infolist(Schema $schema): Schema
    {
        return TenantAccommodationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TenantAccommodationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TenantPricesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenantAccommodations::route('/'),
            'view' => ViewTenantAccommodation::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // کاربر نمی‌تواند accommodation جدید ایجاد کند
    }

    public static function canEdit($record): bool
    {
        return false; // کاربر نمی‌تواند accommodation را ویرایش کند
    }

    public static function canDelete($record): bool
    {
        return false; // کاربر نمی‌تواند accommodation را حذف کند
    }
}
