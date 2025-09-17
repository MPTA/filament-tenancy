<?php

namespace App\Filament\App\Resources\TenantContacts\Pages;

use App\Filament\App\Resources\TenantContacts\TenantContactResource;
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
