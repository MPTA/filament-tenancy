<?php

namespace App\Filament\Tenant\Resources\TenantUsers\Pages;

use App\Filament\Tenant\Resources\TenantUsers\TenantUserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTenantUser extends EditRecord
{
    protected static string $resource = TenantUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
