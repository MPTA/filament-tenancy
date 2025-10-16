<?php

namespace App\Filament\Tenant\Resources\TenantUsers;

use App\Filament\Tenant\Resources\TenantUsers\Pages\CreateTenantUser;
use App\Filament\Tenant\Resources\TenantUsers\Pages\EditTenantUser;
use App\Filament\Tenant\Resources\TenantUsers\Pages\ListTenantUsers;
use App\Filament\Tenant\Resources\TenantUsers\Schemas\TenantUserForm;
use App\Filament\Tenant\Resources\TenantUsers\Tables\TenantUsersTable;
use App\Models\Tenants\TenantUser;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TenantUserResource extends Resource
{
    protected static ?string $model = TenantUser::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $recordTitleAttribute = 'name';
    
    public static function getNavigationLabel(): string
    {
        return __('tenant-users.navigation_label');
    }
    
    public static function getLabel(): ?string
    {
        return __('tenant-users.resource_name');
    }
    
    public static function getPluralLabel(): ?string
    {
        return __('tenant-users.resource_name_plural');
    }
    
    public static function getNavigationGroup(): ?string
    {
        return __('tenant-users.navigation_group');
    }

    public static function form(Schema $schema): Schema
    {
        return TenantUserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TenantUsersTable::configure($table);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['contact', 'contact.country']);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenantUsers::route('/'),
            // 'create' => CreateTenantUser::route('/create'), // Temporarily disabled
            'edit' => EditTenantUser::route('/{record}/edit'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false; // Temporarily disabled
    }
    
    public static function canDelete($record): bool
    {
        return false; // Temporarily disabled
    }
}
