<?php

namespace App\Filament\Tenant\Resources\TenantContacts\Pages;

use App\Filament\Tenant\Resources\TenantContacts\TenantContactResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTenantContact extends ViewRecord
{
    protected static string $resource = TenantContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
