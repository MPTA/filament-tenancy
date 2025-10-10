<?php

namespace App\Filament\Tenant\Resources\TenantUsers\Pages;

use App\Enums\ContactTypeEnum;
use App\Filament\Tenant\Resources\TenantUsers\TenantUserResource;
use App\Models\Contact;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTenantUser extends EditRecord
{
    protected static string $resource = TenantUserResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // بارگذاری داده‌های contact
        $contact = Contact::where('user_id', $this->record->id)->first();
        
        if ($contact) {
            $data['contact'] = [
                'phone' => $contact->phone,
                'mobile' => $contact->mobile,
                'country_id' => $contact->country_id,
                'company' => $contact->company,
                'gender' => $contact->gender,
                'postal_address' => $contact->postal_address,
            ];
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // جدا کردن داده‌های contact از داده‌های user
        $this->contactData = $data['contact'] ?? [];
        unset($data['contact']);
        
        return $data;
    }

    protected function afterSave(): void
    {
        // آپدیت یا ساخت Contact برای User
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

            Contact::updateOrCreate(
                ['user_id' => $this->record->id],
                $contactData
            );
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    private array $contactData = [];
}
