<?php

namespace App\Filament\App\Resources\TenantContacts\Pages;

use App\Enums\ContactTypeEnum;
use App\Filament\App\Resources\TenantContacts\TenantContactResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTenantContact extends EditRecord
{
    protected static string $resource = TenantContactResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Convert type to is_customer for form display
        $data['is_customer'] = ($data['type'] ?? null) === ContactTypeEnum::CUSTOMER->value;
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Set type based on is_customer toggle
        $data['type'] = isset($data['is_customer']) && $data['is_customer'] 
            ? ContactTypeEnum::CUSTOMER->value 
            : ContactTypeEnum::LEAD->value;
        
        // Remove is_customer from data as it's not a database field
        unset($data['is_customer']);
        
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
