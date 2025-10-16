<?php

namespace App\Filament\Tenant\Resources\TenantAttractions;

use App\Filament\Tenant\Resources\TenantAttractions\Pages\ListTenantAttractions;
use App\Filament\Tenant\Resources\TenantAttractions\Pages\ViewTenantAttraction;
use App\Filament\Tenant\Resources\TenantAttractions\Schemas\TenantAttractionInfolist;
use App\Filament\Tenant\Resources\TenantAttractions\Tables\TenantAttractionsTable;
use App\Models\Base\Attraction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TenantAttractionsResource extends Resource
{
    protected static ?string $model = Attraction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;
    
    protected static ?int $navigationSort = 5;
    
    public static function getNavigationLabel(): string
    {
        return __('tenant-attractions.navigation_label');
    }
    
    public static function getLabel(): ?string
    {
        return __('tenant-attractions.resource_name');
    }
    
    public static function getPluralLabel(): ?string
    {
        return __('tenant-attractions.resource_name_plural');
    }

    public static function infolist(Schema $schema): Schema
    {
        return TenantAttractionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TenantAttractionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenantAttractions::route('/'),
            'view' => ViewTenantAttraction::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // کاربر نمی‌تواند attraction جدید ایجاد کند
    }

    public static function canEdit($record): bool
    {
        return false; // کاربر نمی‌تواند attraction را ویرایش کند
    }

    public static function canDelete($record): bool
    {
        return false; // کاربر نمی‌تواند attraction را حذف کند
    }
}