<?php

namespace App\Filament\Tenant\Resources\TenantUsers\Pages;

use App\Filament\Tenant\Resources\TenantUsers\TenantUserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTenantUser extends CreateRecord
{
    protected static string $resource = TenantUserResource::class;
}
