<?php

namespace App\Filament\App\Resources\TenantContacts\Pages;

use App\Filament\App\Resources\TenantContacts\TenantContactResource;
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
