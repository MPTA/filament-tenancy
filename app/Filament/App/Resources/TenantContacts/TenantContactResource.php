<?php

namespace App\Filament\App\Resources\TenantContacts;

use App\Filament\App\Resources\TenantContacts\Pages\CreateTenantContact;
use App\Filament\App\Resources\TenantContacts\Pages\EditTenantContact;
use App\Filament\App\Resources\TenantContacts\Pages\ListTenantContacts;
use App\Filament\App\Resources\TenantContacts\Pages\ViewTenantContact;
use App\Filament\App\Resources\TenantContacts\Schemas\TenantContactForm;
use App\Filament\App\Resources\TenantContacts\Schemas\TenantContactInfolist;
use App\Filament\App\Resources\TenantContacts\Tables\TenantContactsTable;
use App\Models\Tenants\TenantContact;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TenantContactResource extends Resource
{
    protected static ?string $model = TenantContact::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $recordTitleAttribute = 'first_name';

    protected static ?string $navigationLabel = 'Contacts';

    protected static ?string $modelLabel = 'Contact';

    protected static ?string $pluralModelLabel = 'Contacts';

    protected static string|\UnitEnum|null $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->first_name . ' ' . $record->last_name . ' (' . $record->email . ')';
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Email' => $record->email ?? 'No email',
            'Phone' => $record->phone ?? 'No phone',
            'Company' => $record->company ?? 'No company',
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'last_name', 'email', 'phone', 'company'];
    }

    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl('view', ['record' => $record]);
    }

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
