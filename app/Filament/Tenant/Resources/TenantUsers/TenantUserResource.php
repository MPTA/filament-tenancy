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

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TenantUserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TenantUsersTable::configure($table);
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
            'create' => CreateTenantUser::route('/create'),
            'edit' => EditTenantUser::route('/{record}/edit'),
        ];
    }
}
