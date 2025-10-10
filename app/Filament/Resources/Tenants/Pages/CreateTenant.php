<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Enums\ContactTypeEnum;
use App\Filament\Resources\Tenants\TenantResource;
use App\Models\Contact;
use App\Models\TenantSetting;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // جدا کردن داده‌های settings از داده‌های tenant
        $this->settingsData = $data['settings'] ?? [];
        unset($data['settings']);
        
        // ذخیره name برای استفاده بعدی در user و contact
        $this->contactPersonName = $data['name'] ?? null;
        
        // تبدیل tenant_name به name برای ذخیره در جدول tenants
        if (isset($data['tenant_name'])) {
            $data['name'] = $data['tenant_name'];
            unset($data['tenant_name']);
        }
        
        // اگر id ست نشده، از name بسازیم
        if (empty($data['id'])) {
            $data['id'] = Str::slug($data['name'], '_');
        }
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // ساخت domain برای tenant
        if ($this->data['domain'] ?? null) {
            $this->record->domains()->create([
                'domain' => $this->data['domain'],
            ]);
        }

        // ساخت tenant settings
        $tenantSettings = null;
        if (!empty($this->settingsData)) {
            $tenantSettings = TenantSetting::create([
                'tenant_id' => $this->record->id,
                'contact_name' => $this->contactPersonName,
                ...$this->settingsData,
            ]);
        }

        // ساخت user برای tenant (فقط در single database mode)
        if (config('filament-tenancy.single_database')) {
            $userData = [
                'name' => $this->contactPersonName ?? $this->record->name,
                'email' => $this->record->email,
                'password' => $this->record->password,
                'tenant_id' => $this->record->id,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            $userModelClass = config('filament-tenancy.tenant_user_model', \App\Models\User::class);
            
            // Initialize tenant context برای tenant_id مناسب
            tenancy()->initialize($this->record);
            
            $user = $userModelClass::updateOrCreate(
                [
                    'email' => $userData['email'],
                    'tenant_id' => $this->record->id,
                ],
                $userData
            );
            
            // ساخت Contact برای User
            Contact::updateOrCreate(
                [
                    'user_id' => $user->id,
                ],
                [
                    'first_name' => $this->contactPersonName ?? $this->record->name,
                    'last_name' => null,
                    'email' => $this->record->email,
                    'phone' => $tenantSettings?->phone_number ?? null,
                    'mobile' => $tenantSettings?->mobile_number ?? null,
                    'postal_address' => $tenantSettings?->address ?? null,
                    'company' => $tenantSettings?->company_name ?? null,
                    'type' => ContactTypeEnum::USER->value,
                    'country_id' => $tenantSettings?->country_id,
                    'tenant_id' => $this->record->id,
                    'user_id' => $user->id,
                ]
            );
            
            // End tenant context
            tenancy()->end();
        }
    }

    private array $settingsData = [];
    private ?string $contactPersonName = null;
}
