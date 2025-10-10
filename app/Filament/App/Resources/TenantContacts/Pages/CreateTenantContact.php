<?php

namespace App\Filament\App\Resources\TenantContacts\Pages;

use App\Enums\ContactTypeEnum;
use App\Filament\App\Resources\TenantContacts\TenantContactResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTenantContact extends CreateRecord
{
    protected static string $resource = TenantContactResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set type based on is_customer toggle
        $data['type'] = isset($data['is_customer']) && $data['is_customer'] 
            ? ContactTypeEnum::CUSTOMER->value 
            : ContactTypeEnum::LEAD->value;
        
        // Remove is_customer from data as it's not a database field
        unset($data['is_customer']);
        
        return $data;
    }
}
