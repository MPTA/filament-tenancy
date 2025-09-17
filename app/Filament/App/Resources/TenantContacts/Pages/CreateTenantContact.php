<?php

namespace App\Filament\App\Resources\TenantContacts\Pages;

use App\Filament\App\Resources\TenantContacts\TenantContactResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTenantContact extends CreateRecord
{
    protected static string $resource = TenantContactResource::class;
}
