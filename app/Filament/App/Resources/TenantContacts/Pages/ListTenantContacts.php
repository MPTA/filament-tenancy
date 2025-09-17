<?php

namespace App\Filament\App\Resources\TenantContacts\Pages;

use App\Filament\App\Resources\TenantContacts\TenantContactResource;
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
