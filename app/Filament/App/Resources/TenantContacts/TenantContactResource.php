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

    protected static ?string $navigationLabel = null;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static string|UnitEnum|null $navigationGroup = null;

    public static function getNavigationLabel(): string
    {
        return __('app-contacts.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('app-contacts.resource_name');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app-contacts.resource_name_plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('app-contacts.navigation_group');
    }

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('type', [
            \App\Enums\ContactTypeEnum::CUSTOMER,
            \App\Enums\ContactTypeEnum::LEAD,
        ])->count();
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
            __('app-contacts.global_search.email') => $record->email ?? __('app-contacts.placeholders.no_email'),
            __('app-contacts.global_search.phone') => $record->phone ?? __('app-contacts.placeholders.no_phone'),
            __('app-contacts.global_search.company') => $record->company ?? __('app-contacts.placeholders.no_company'),
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
