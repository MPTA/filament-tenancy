<?php

namespace App\Filament\Tenant\Resources\TenantContacts\Pages;

use App\Filament\Tenant\Resources\TenantContacts\TenantContactResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTenantContacts extends ListRecords
{
    protected static string $resource = TenantContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
