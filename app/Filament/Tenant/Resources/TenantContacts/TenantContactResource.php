<?php

namespace App\Filament\Tenant\Resources\TenantContacts;

use App\Filament\Tenant\Resources\TenantContacts\Pages\CreateTenantContact;
use App\Filament\Tenant\Resources\TenantContacts\Pages\EditTenantContact;
use App\Filament\Tenant\Resources\TenantContacts\Pages\ListTenantContacts;
use App\Filament\Tenant\Resources\TenantContacts\Pages\ViewTenantContact;
use App\Filament\Tenant\Resources\TenantContacts\Schemas\TenantContactForm;
use App\Filament\Tenant\Resources\TenantContacts\Schemas\TenantContactInfolist;
use App\Filament\Tenant\Resources\TenantContacts\Tables\TenantContactsTable;
use App\Models\Tenants\TenantContact;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TenantContactResource extends Resource
{
    protected static ?string $model = TenantContact::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'first_name';

    public static function form(Schema $schema): Schema
    {
        return TenantContactForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TenantContactInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TenantContactsTable::configure($table);
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
            'index' => ListTenantContacts::route('/'),
            'create' => CreateTenantContact::route('/create'),
            'view' => ViewTenantContact::route('/{record}'),
            'edit' => EditTenantContact::route('/{record}/edit'),
        ];
    }
}
