<?php

namespace App\Filament\Tenant\Resources\TenantUsers\Pages;

use App\Enums\ContactTypeEnum;
use App\Filament\Tenant\Resources\TenantUsers\TenantUserResource;
use App\Models\Contact;
use Filament\Resources\Pages\CreateRecord;

class CreateTenantUser extends CreateRecord
{
    protected static string $resource = TenantUserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // جدا کردن داده‌های contact از داده‌های user
        $this->contactData = $data['contact'] ?? [];
        unset($data['contact']);
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // ساخت Contact برای User
        if ($this->record) {
            $contactData = [
                'first_name' => $this->record->name,
                'last_name' => null,
                'email' => $this->record->email,
                'type' => ContactTypeEnum::USER->value,
                'tenant_id' => $this->record->tenant_id,
                'user_id' => $this->record->id,
            ];

            // اضافه کردن داده‌های اضافی contact اگر وجود داشتند
            if (!empty($this->contactData)) {
                $contactData = array_merge($contactData, array_filter($this->contactData));
            }

            Contact::create($contactData);
        }
    }

    private array $contactData = [];
}
