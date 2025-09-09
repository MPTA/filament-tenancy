<?php

namespace App\Filament\Tenant\Resources\TenantContacts\Pages;

use App\Filament\Tenant\Resources\TenantContacts\TenantContactResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTenantContact extends CreateRecord
{
    protected static string $resource = TenantContactResource::class;
}
