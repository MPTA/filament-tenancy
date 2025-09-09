<?php

namespace App\Filament\Tenant\Resources\TenantContacts\Pages;

use App\Filament\Tenant\Resources\TenantContacts\TenantContactResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTenantContact extends EditRecord
{
    protected static string $resource = TenantContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
